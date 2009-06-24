#!/usr/bin/perl

####
# Usage examples
#
# time perl piwigo_remote.pl \
#   --action=pwg.images.add \
#   --file=erwann_rocher-web.jpg \
#   --thumb=erwann_rocher-thumb.jpg \
#   --high=erwann_rocher-high.jpg \
#   --original=erwann_rocher-high.jpg \
#   --define categories=9 \
#   --chunk_size=200_000

use strict;
use warnings;

use JSON;
use LWP::UserAgent;
use Getopt::Long;
use Encode qw/is_utf8 decode/;
use POSIX qw(ceil floor);

my %opt = ();
GetOptions(
    \%opt,
    qw/
          action=s
          file=s
          thumbnail=s
          high=s
          original=s
          categories=s
          chunk_size=i
          base_url=s
          username=s
          password=s
          define=s%
      /
);

our $ua = LWP::UserAgent->new;
$ua->cookie_jar({});

my %conf;
$conf{response_format} = 'json';
$conf{limit} = 10;

my %conf_default = (
    base_url => 'http://localhost/piwigo/2.0',
    username => 'plg',
    password => 'plg',
    chunk_size => 500_000,
);
foreach my $conf_key (keys %conf_default) {
    $conf{$conf_key} = defined $opt{$conf_key} ? $opt{$conf_key} : $conf_default{$conf_key}
}

my $result = undef;
my $query = undef;

binmode STDOUT, ":encoding(utf-8)";

# TODO : don't connect at each script call, use the session duration instead.
my $form = {
    method => 'pwg.session.login',
    username => $conf{username},
    password => $conf{password},
};

$result = $ua->post(
    $conf{base_url}.'/ws.php?format=json',
    $form
);

# print "\n", $ua->cookie_jar->as_string, "\n";

if ($opt{action} eq 'pwg.images.add') {
    use MIME::Base64 qw(encode_base64);
    use Digest::MD5::File qw/file_md5_hex/;
    use File::Slurp;

    $form = {};
    $form->{method} = 'pwg.images.add';

    my $original_sum = file_md5_hex($opt{original});
    $form->{original_sum} = $original_sum;

    send_chunks(
        filepath => $opt{file},
        type => 'file',
        original_sum => $original_sum,
    );
    $form->{file_sum} = file_md5_hex($opt{file});

    send_chunks(
        filepath => $opt{thumbnail},
        type => 'thumb',
        original_sum => $original_sum,
    );
    $form->{thumbnail_sum} = file_md5_hex($opt{thumbnail});

    if (defined $opt{high}) {
        send_chunks(
            filepath => $opt{high},
            type => 'high',
            original_sum => $original_sum,
        );
        $form->{high_sum} = file_md5_hex($opt{high});
    }

    foreach my $key (keys %{ $opt{define} }) {
        $form->{$key} = $opt{define}{$key};
    }

    my $response = $ua->post(
        $conf{base_url}.'/ws.php?format=json',
        $form
    );

    print "-" x 50, "\n";
    printf("response code    : %u\n", $response->code);
    printf("response message : %s\n", $response->message);
    print "-" x 50, "\n";
    print "\n";

#     use Data::Dumper;
#     print Dumper($response->content);
#     print Dumper(from_json($response->content));

    if ($response->is_success) {
        print "upload successful\n";
    }
    else {
        warn 'A problem has occured during upload', "\n";
        warn $response->decoded_content, "\n";
        die $response->status_line;
    }
}

if ($opt{action} eq 'pwg.tags.list') {
    use Text::ASCIITable;

    $query = pwg_ws_get_query(
        method => 'pwg.tags.getList',
        sort_by_counter => 'true',
    );

    $result = $ua->get($query);
    my $tag_result = from_json($result->content);
    my $t = Text::ASCIITable->new({ headingText => 'Tags' });
    $t->setCols('id','counter','name');

    my $tag_number = 1;
    foreach my $tag_href (@{ $tag_result->{result}{tags} }) {
        $t->addRow(
            $tag_href->{id},
            $tag_href->{counter},
            $tag_href->{name}
        );

        last if $tag_number++ >= $conf{limit};
    }
    print $t;
}

if ($opt{action} eq 'pwg.tags.getAdminList') {
    $query = pwg_ws_get_query(
        method => 'pwg.tags.getAdminList'
    );

    $result = $ua->get($query);
    my $tags = from_json($result->content)->{result}{tags};

    foreach my $tag (@{$tags}) {
        # print join(',', keys %{$tag}), "\n"; exit();
        printf(
            '{%u} %s ',
            $tag->{id},
            $tag->{name}
        );
    }

    print "\n";
}

if ($opt{action} eq 'pwg.categories.add') {
    $form = {
        method => 'pwg.categories.add',
        name => $opt{define}{name},
        parent => $opt{define}{parent},
    };

    my $response = $ua->post(
        $conf{base_url}.'/ws.php?format=json',
        $form
    );

    use Data::Dumper;
    print Dumper(from_json($response->content));
}

if ($opt{action} eq 'pwg.tags.add') {
    $form = {
        method => 'pwg.tags.add',
        name => $opt{define}{name},
    };

    my $response = $ua->post(
        $conf{base_url}.'/ws.php?format=json',
        $form
    );

    use Data::Dumper;
    print Dumper(from_json($response->content));
}

if ($opt{action} eq 'pwg.images.exist') {
    $form = {
        method => $opt{action},
    };

    foreach my $key (keys %{ $opt{define} }) {
        $form->{$key} = $opt{define}{$key};
    }

    my $response = $ua->post(
        $conf{base_url}.'/ws.php?format=json',
        $form
    );

    use Data::Dumper;
    print Dumper(from_json($response->content)->{result});
    # print Dumper($response);
}

if ($opt{action} eq 'pwg.images.setInfo' or $opt{action} eq 'pwg.categories.setInfo') {
    $form = {
        method => $opt{action},
    };

    foreach my $key (keys %{ $opt{define} }) {
        $form->{$key} = $opt{define}{$key};
    }

    my $response = $ua->post(
        $conf{base_url}.'/ws.php?format=json',
        $form
    );

    use Data::Dumper;
    # print Dumper(from_json($response->content)->{result});
    print Dumper($response);
}

$query = pwg_ws_get_query(
    method => 'pwg.session.logout'
);
$ua->get($query);

sub pwg_ws_get_query {
    my %params = @_;

    my $query = $conf{base_url}.'/ws.php?format='.$conf{response_format};

    foreach my $key (keys %params) {
        $query .= '&'.$key.'='.$params{$key};
    }

    return $query;
}

sub send_chunks {
    my %params = @_;

    my $content = read_file($params{filepath});
    my $content_length = length($content);
    my $nb_chunks = ceil($content_length / $conf{chunk_size});

    my $chunk_pos = 0;
    my $chunk_id = 1;
    while ($chunk_pos < $content_length) {
        my $chunk = substr(
            $content,
            $chunk_pos,
            $conf{chunk_size}
        );
        $chunk_pos += $conf{chunk_size};

        my $response = $ua->post(
            $conf{base_url}.'/ws.php?format=json',
            {
                method => 'pwg.images.addChunk',
                data => encode_base64($chunk),
                original_sum => $params{original_sum},
                position => $chunk_id,
                type => $params{type},
            }
        );

        printf(
            'chunk %05u of %05u for %s "%s"'."\n",
            $chunk_id,
            $nb_chunks,
            $params{type},
            $params{filepath}
        );
        if ($response->code != 200) {
            printf("response code    : %u\n", $response->code);
            printf("response message : %s\n", $response->message);
        }

        $chunk_id++;
    }
}

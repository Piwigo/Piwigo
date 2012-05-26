#!/usr/bin/perl

####
# Usage examples
#
# perl piwigo_remote.pl --action=pwg.images.add --file=erwann_rocher-web.jpg --define categories=9

use strict;
use warnings;

use JSON;
use LWP::UserAgent;
# LWP::Debug::level('+');
use Getopt::Long;
use Encode qw/is_utf8 decode/;
use POSIX qw(ceil floor);

my %opt = ();
GetOptions(
    \%opt,
    qw/
          action=s
          file=s
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
$ua->agent('Mozilla/piwigo_remote.pl 1.25');
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

$ua->default_headers->authorization_basic(
    $conf{username},
    $conf{password}
);

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
    use Digest::MD5::File qw/file_md5_hex/;

    $form = {};
    $form->{method} = $opt{action};

    my $original = $opt{file};
    if (defined $opt{original}) {
        $original = $opt{original};
    }

    my $original_sum = file_md5_hex($original);
    $form->{original_sum} = $original_sum;

    send_chunks(
        filepath => $opt{file},
        type => 'file',
        original_sum => $original_sum,
    );
    $form->{file_sum} = file_md5_hex($opt{file});

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
        print Dumper($response);
        warn 'A problem has occured during upload', "\n";
        warn $response->decoded_content, "\n";
        die $response->status_line;
    }
}

if ($opt{action} eq 'pwg.images.addFile') {
    use Digest::MD5::File qw/file_md5_hex/;

    if (not defined $opt{define}{image_id}) {
        die '--define image_id=1234 is missing';
    }

    # which file type are we going to add/update?
    my $type = undef;

    foreach my $test_type (qw/thumbnail file high/) {
        if (defined $opt{$test_type}) {
            $type = $test_type;
            last;
        }
    }

    if (not defined $type) {
        die 'at least one of file/thumbnail/high parameters must be set';
    }

    my $type_code = typecode_from_typename($type);

    send_chunks(
        filepath => $opt{$type},
        type => $type_code,
        original_sum => file_md5_hex($opt{original}),
    );

    $form = {};
    $form->{method} = $opt{action};
    $form->{type}   = $type_code;
    $form->{sum}    = file_md5_hex($opt{$type});

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
        print Dumper($response);
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
    print Dumper($result);
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

if ($opt{action} eq 'pwg.images.checkFiles') {
    use Digest::MD5::File qw/file_md5_hex/;

    $form = {};
    $form->{method} = $opt{action};

    foreach my $type (qw/thumbnail file high/) {
        if (defined $opt{$type}) {
            $form->{$type.'_sum'} = file_md5_hex($opt{$type});
        }
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

    use Data::Dumper;
    print Dumper(from_json($response->content));
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

if ($opt{action} eq 'pwg.categories.getList') {
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
    print Dumper($response->content);
    print Dumper(from_json($response->content)->{result});
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

    use MIME::Base64 qw(encode_base64);
    use File::Slurp;

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

sub typecode_from_typename {
    my ($typename) = @_;

    my $typecode = $typename;

    if ('thumbnail' eq $typename) {
        $typecode = 'thumb';
    }

    return $typecode;
}

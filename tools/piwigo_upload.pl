#!/usr/bin/perl

####
# Usage
#
# perl piwigo_upload.pl --url=http://piwigo.org/demo --user=admin --password=secret --file=photo.jpg --album_id=9

use strict;
use warnings;

use JSON;
use LWP::UserAgent;
use Getopt::Long;
use POSIX qw(ceil floor);
use Digest::MD5 qw/md5 md5_hex/;
use File::Slurp;
use File::Basename;

my %opt = ();
GetOptions(
    \%opt,
    qw/
          file=s
          album_id=i
          category=s
          url=s
          username=s
          password=s
      /
);

our %conf = (
    chunk_size => 500_000,
);

our $ua = LWP::UserAgent->new;
$ua->agent('Mozilla/piwigo_upload.pl 1.56');
$ua->cookie_jar({});

my $result = undef;

my $form = {
    method => 'pwg.session.login',
    username => $opt{username},
    password => $opt{password},
};

$result = $ua->post(
    $opt{url}.'/ws.php?format=json',
    $form
);

my $response = $ua->post(
    $opt{url}.'/ws.php?format=json',
    {
        method => 'pwg.session.getStatus',
    }
);

my $pwg_token = from_json($response->content)->{result}->{pwg_token};

my $content = read_file($opt{file});
my $content_length = length($content);
my $nb_chunks = ceil($content_length / $conf{chunk_size});

my $chunk_pos = 0;
my $chunk_id = 0;

while ($chunk_pos < $content_length) {
    my $chunk = substr(
        $content,
        $chunk_pos,
        $conf{chunk_size}
    );

    # write the chunk as a temporary local file
    my $chunk_path = '/tmp/'.md5_hex($opt{file}).'.chunk';

    open(my $ofh, '>'.$chunk_path) or die "problem for writing temporary local chunk";
    print {$ofh} $chunk;
    close($ofh);

    $chunk_pos += $conf{chunk_size};

    my $response = $ua->post(
        $opt{url}.'/ws.php?format=json',
        {
            method => 'pwg.images.upload',
            chunk => $chunk_id,
            chunks => $nb_chunks,
            category => $opt{album_id},
            pwg_token => $pwg_token,
            file => [$chunk_path],
            name => basename($opt{file}),
        },
        'Content_Type' => 'form-data',
    );

    unlink($chunk_path);

    printf(
        'chunk %03u of %03u for "%s"'."\n",
        $chunk_id+1,
        $nb_chunks,
        $opt{file}
    );

    if ($response->code != 200) {
        printf("response code    : %u\n", $response->code);
        printf("response message : %s\n", $response->message);
    }

    $chunk_id++;
}

$result = $ua->get(
    $opt{url}.'/ws.php?format=json',
    {
        method => 'pwg.session.logout'
    }
);

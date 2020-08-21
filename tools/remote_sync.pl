#!/usr/bin/perl

# perl remote_sync.pl --base_url=http://localhost/piwigo/dev/branches/2.0 --username=plg --password=plg

use strict;
use warnings;

use LWP::UserAgent;
use Getopt::Long;

my %opt = ();
GetOptions(
    \%opt,
    qw/
          base_url=s
          username=s
          password=s
      /
);

our $ua = LWP::UserAgent->new;
$ua->agent('Mozilla/remote_sync.pl');
$ua->cookie_jar({});

$ua->default_headers->authorization_basic(
    $opt{username},
    $opt{password}
);

my $form = {
    method => 'pwg.session.login',
    username => $opt{username},
    password => $opt{password},
};

my $result = $ua->post(
    $opt{base_url}.'/ws.php?format=json',
    $form
);

# perform the synchronization
$form = {
    'sync'             => 'files',
    'display_info'     => 1,
    'add_to_caddie'    => 1,
    'privacy_level'    => 0,
    'sync_meta'        => 1, # remove this parameter, turning to 0 is not enough
    'simulate'         => 0,
    'subcats-included' => 1,
    'submit'           => 1,
};

$result = $ua->post(
    $opt{base_url}.'/admin.php?page=site_update&site=1',
    $form
);

use Data::Dumper;
print Dumper($result);
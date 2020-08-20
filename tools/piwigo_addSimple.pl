#!/usr/bin/perl
 
use LWP::UserAgent;
 
my %conf = (
    base_url => 'http://localhost/pwggit',
);
 
my $ua = LWP::UserAgent->new;
$ua->cookie_jar({});
 
$ua->post(
    $conf{base_url}.'/ws.php',
    {
        method => 'pwg.session.login',
        username => 'plg',
        password => 'plg',
    }
);
 
my $response = $ua->post(
    $conf{base_url}.'/ws.php',
    {
        method => 'pwg.images.addSimple',
        image => ['/Users/plg/Documents/IMG_7779.jpg'],
        category => 6,
        tags => 'tag1, tag2, another tag',
        name => 'A nice title',
        comment => 'A longer description',
        author => 'Paul Nikanon',
        level => 0,
    },
    'Content_Type' => 'form-data',
);

use Data::Dumper; print Dumper($response->message);
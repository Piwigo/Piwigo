#!/usr/bin/perl
 
# Usage:
# mojo_uploader [--config=cfgfile] --url=http://piwigo.org/demo --user=admin --password=secret --album_id=9 photo.jpg ...
 
use 5.024; 
use strict;
use warnings;
use FindBin;
use lib "$FindBin::Bin/lib";
use Mojo::Piwigo::Client;
use Mojo::Piwigo::Client::Config;
use Getopt::Long 'GetOptionsFromArray';
use List::Util qw/ all /;
use Try::Tiny;
use YAML::XS 'LoadFile';

exit run( @ARGV );

sub run {
    my @args = @_;
    my %opt;

    GetOptionsFromArray(\@args, \%opt,
        qw/

        config|c=s
        album_id|a=i
        url|U=s
        username|u=s
        password|p=s

        /
    );

    unless( @args >= 1 ) {
        say STDERR "Specify at least one file to upload";
        usage(1);
    }

    my $cfg = try {
        Mojo::Piwigo::Client::Config->new( \%opt );
    } catch {
        say STDERR $_;
        usage(1);
    };
    my $cl = Mojo::Piwigo::Client->new(
        Mojo::Piwigo::Client::Config->new( \%opt )->cfg
    );
    $cl->ua->request_timeout( 20 );
    $cl->login;
    $cl->upload( $_, $opt{album_id} ) for @args;
    $cl->logout;
}

# Print program usage and exit with the argument value
sub usage {
    my ($prog) = $0 =~ m!(?:.*/)?(.*)!;
    print <<EOF;
Usage: $prog --url=URL --user=NAME --password=PW --album_id=ID <file>
EOF
    exit(shift // 0);
}

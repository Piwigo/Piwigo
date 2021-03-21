#!/usr/bin/perl
 
# Usage
# mojo_upload_dirs.pl [--cfg=cfgfile] --url=URL --user=ADMIN --password=PW --parent=name <dir>
 
use 5.024; 
use strict;
use warnings;
use FindBin;
use lib "$FindBin::Bin/lib";
use Mojo::Piwigo::Client;
use Mojo::Piwigo::Client::Config;
use Getopt::Long 'GetOptionsFromArray';
use List::Util qw/ all first /;
use Path::Tiny;
use Try::Tiny;
use IO::Handle;
use YAML::XS qw/LoadFile Dump/;

exit run( @ARGV );

sub run {
    my @args = @_;
    my %opt;

    GetOptionsFromArray(\@args, \%opt,
        qw/

        config|c=s
        parent|p=s
        url|U=s
        user|u=s
        password|p=s

        /
    );

    my $cfg = try {
        Mojo::Piwigo::Client::Config->new( \%opt );
    } catch {
        say STDERR $_;
        usage(1);
    };

    my $cl = Mojo::Piwigo::Client->new( $cfg->cfg )->login;

    my $parent_id = get_parent_id( $cl, $opt{parent} );
    upload_hierarchy( $cl, $_, $parent_id ) for @args;
    return 0;
}

# Given a Piwigo client, directory and a parent album, upload the directory and
# all its subdirectories, reproducing the directory structure as albums
sub upload_hierarchy {
    my ($cl, $dir, $parent_id) = @_;
    my $root = path( $dir );
    unless( $root->is_dir ) {
        say STDERR "$dir is not a directory, skipping";
        return;
    }
    recurse_dir( $cl, $root, $parent_id );
}

# The actual uploading function. Traverses a directory, creates all
# subdirs as albums, and uploads all JPEG files.
# TODO: configurable filter instead of hardcoded JPEG-only
sub recurse_dir {
    my ($cl, $root, $parent_id) = @_;
    my %cats = map { $_->{name} => $_->{id} } $cl->categories( cat_id => $parent_id )->@*;
    my $it = $root->iterator;
    while( my $p = $it->() ) {
        if( $p->is_dir ) {
            my $rel = $p->relative( $root );
            my $id = $cats{"$rel"} //= $cl->add_category( "$rel", parent => $parent_id );
            say "DIR: $p => $id";
            recurse_dir( $cl, $p, $id );
        } else {
            STDOUT->printflush( "UPLOAD: $p" );
            unless( "$p" =~ /\.jpe?g$/i ) {
                STDOUT->printflush( "wrong type, skipped\n" );
                next;
            }
            try {
                $cl->upload( "$p", $parent_id, sub {
                        STDOUT->printflush( ( $_[2] == $_[3]-1 ) ? "\n" : ".");
                    }
                );
            } catch {
                STDOUT->printflush( "error: $_\n" );
            };
        }
    }
}

# Given a Piwigo client and the value of the --parent option, figures
# out the right ID:
# * If the option is undefined, just return 0, i.e. "no parent"
# * If the option is a number and corresponds to an existing album ID,
#   just return it.
# * Otherwise, interpret the ID as a path separated by slashes (e.g.
#   "Community/My Album/foo"), try to look that up in the album hierarchy
#   and return its ID.
# * Failing that, check whether the option uniquely identifies an album
#   anywhere in the hierarchy, and if so, return its ID.
sub get_parent_id {
    my ($cl, $opt) = @_;

    return 0 unless defined $opt;

    my @flatcats = $cl->categories( recursive => 1 )->@*;
    $opt =~ /^[0-9]+$/
        and first { $_->{id} == $opt } @flatcats
        and return $opt;

    my @path = split /\//, $opt;
    my $id = descend_album_path(
        [ split /\//, $opt ],
        $cl->categories( recursive => 1, tree_output => 1 )
    );
    return $id if defined $id;
    my @matching = grep { $_->{name} == $opt } @flatcats;
    return $matching[0]{id} if @matching == 1;
    die "could not find parent album $opt\n";
}

# Given an array of path components and an array tree of albums,
# walk the tree according to the path and return the final ID
sub descend_album_path {
    my ($path, $cats) = @_;
    my $name = shift @$path;
    my $cat = first { $_->{name} eq $name } @$cats
        or die "no category found for path component $name\n";
    return $cat->{id} unless @$path;
    return descend_album_path( $path, $cat->{sub_categories} );
}

# Print program usage and exit with the argument value
sub usage {
    my ($prog) = $0 =~ m!(?:.*/)?(.*)!;
    print <<EOF;
Usage: $prog --url=URL --user=NAME --password=PW --album_id=ID <file>
EOF
    exit(shift // 0);
}

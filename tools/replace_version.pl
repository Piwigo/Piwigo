#!/usr/bin/perl

####
# Usage
#
# perl replace_version.pl --file=/path/to/file.php --version=2.8.0

use strict;
use warnings;

use Getopt::Long;
use File::Basename;

my %opt = ();
GetOptions(
    \%opt,
    qw/
          file=s
          version=s
      /
);

if (not -e $opt{file}) {
    die "file missing ".$opt{file};
}

my $new_content = '';
open(my $ifh, '<'.$opt{file}) or die 'Houston, problem with "'.$opt{file}.'" for reading';
while (<$ifh>) {
    if (/^Version:/) {
        $_ = 'Version: '.$opt{version}.''."\n";
    }
    $new_content.= $_;
}
close($ifh);

open(my $ofh, '>'.$opt{file}) or die 'Houston, problem with "'.$opt{file}.'" for writing';
print {$ofh} $new_content;
close($ofh);

#!/usr/bin/perl

use strict;
use warnings;

my $replacement_file = $ARGV[0];
my $language_dir = $ARGV[1];

# load the replacements
my %replace_by = ();
open(my $ifh_rep, '<'.$replacement_file);
while (<$ifh_rep>) {
    if (m/^\$lang\['(.*)'\] \s* = \s* (['"])(.*)\2;/x) {
        if ($1 ne $3 and length($1) > 0) {
            $replace_by{$1} = $3;
        }
    }
}
# use Data::Dumper; print Dumper(\%replace_by); exit();

my $append_to_common = '';

foreach my $file_code (qw/upgrade install admin common plugin/) {
    my $filename = $language_dir.'/'.$file_code.'.lang.php';
    # print $filename;
    if (not -f $filename) {
        # print ' is missing', "\n";
        next;
    }
    print $filename.' is under process', "\n";

    my $file_content = '';

    open(my $ifh, '<'.$filename);
    while (my $line = <$ifh>) {
        if ($line =~ m/^\$lang\['(.*)'\] \s* =/x) {
            if (defined $replace_by{$1}) {
                my $search = quotemeta($1);
                my $replace = $replace_by{$1};

                $line =~ s{$search}{$replace};
                $file_content.= $line;
            }
            else {
                $file_content.= $line;
            }
        }
        elsif ($line =~ m/^?>/) {
            $file_content.= $line;
        }
        else {
            $file_content.= $line;
        }
    }
    close($ifh);

    open(my $ofh, '>'.$filename);
    print {$ofh} $file_content;
    close($ofh);
}

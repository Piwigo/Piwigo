#!/usr/bin/perl

use strict;
use warnings;

my $replacement_file = $ARGV[0];
my $language_dir = $ARGV[1];

# load the new values for given keys
my %new_value_of = ();
open(my $ifh_rep, '<'.$replacement_file);
while (<$ifh_rep>) {
    if (m/^\$lang\['(.*)'\] \s* = \s* (.*);/x) {
        $new_value_of{$1} = $2;
    }
}
# use Data::Dumper; print Dumper(\%new_value_of); exit();

my %replacement_performed_for = ();

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
            if (defined $new_value_of{$1}) {
                $file_content.= '$lang[\''.$1.'\'] = '.$new_value_of{$1}.';'."\n";
                $replacement_performed_for{$1}++;
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

foreach my $new_value (keys %new_value_of) {
    if (not defined $replacement_performed_for{$new_value}) {
        print 'no replacement performed on: ', $new_value, "\n";
    }
}

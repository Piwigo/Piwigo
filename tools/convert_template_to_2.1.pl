#!/usr/bin/perl

# perl tools/convert_template_to_2.1.pl /path/to/tpl/files/directory

use strict;
use warnings;

use File::Find;

my $base_dir = $ARGV[0];

# load the replacements
my @file_codes = qw/upgrade install admin common/;
my %replace_by = ();
foreach my $file_code (@file_codes) {
    open(my $ifh_rep, '<language/templates/'.$file_code.'.lang.php');
    while (<$ifh_rep>) {
        if (m/^\$lang\['(.*)'\] \s* = \s* (['"])(.*)\2;/x) {
            if ($1 ne $3 and length($1) > 0) {
                $replace_by{$1} = $3;
            }
        }
    }
}
# use Data::Dumper; print Dumper(\%replace_by); exit();

find(\&replace_keys, $base_dir);

sub replace_keys {
    if ($File::Find::name !~ m/tpl$/) {
        return 0;
    }

    my $file_content = '';
    open(my $fhi, '<', $File::Find::name);
    while (<$fhi>) {
        foreach my $from (keys %replace_by) {
            my $to = $replace_by{$from};
            s/{'$from'\|\@translate/{'$to'|\@translate/g;
        }
        $file_content.= $_;
    }
    close($fhi);

    open(my $fho, '>', $File::Find::name);
    print {$fho} $file_content;
    close($fho);
}

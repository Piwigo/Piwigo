#!/usr/bin/perl

# Here is the way I finally used this script:
#
# for language in ar_SA cz_CZ da_DK de_DE es_AR es_ES hr_HR hu_HU it_IT ja_JP nl_NL pl_PL pt_BR pt_PT ru_RU sr_RS vi_VN zh_CN
# do
#   export PWG_LANG=$language
#   rm -rf language/$PWG_LANG
#   cp -r ../branches/2.0/language/$PWG_LANG language/
#   perl tools/convert_language_to_2.1.pl language/$PWG_LANG
# done

use strict;
use warnings;

my $language_dir = $ARGV[0];

my @file_codes = qw/upgrade install admin common/;

my %ignore_keys = (
    'user_status_admin' => '',
    'user_status_generic' => '',
    'user_status_guest' => '',
    'user_status_normal' => '',
    'user_status_webmaster' => '',
    'Level 0' => '',
    'Level 1' => '',
    'Level 2' => '',
    'Level 4' => '',
    'Level 8' => '',
    'ACCESS_0' => '',
    'ACCESS_1' => '',
    'ACCESS_2' => '',
    'ACCESS_3' => '',
    'ACCESS_4' => '',
    'ACCESS_5' => '',
    'month' => '',
    'day' => '',
    'chronology_monthly_calendar' => '',
    'chronology_monthly_list' => '',
    'chronology_weekly_list' => '',
);

my %remove_keys = (
    admin => {
        'nbm_content_goto_2' => '',
        'nbm_content_hello_2' => '',
        'nbm_redirect_msg' => '',
    },
    upgrade => {
        'Are you sure?' => '',
    },
);

my %to_copy = ();

# load the replacements
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

my $append_to_common = '';

foreach my $file_code (@file_codes) {
    my $filename = $language_dir.'/'.$file_code.'.lang.php';
    print $filename;
    if (not -f $filename) {
        print ' is missing', "\n";
        next;
    }
    print ' is under process', "\n";

    if ($file_code eq 'admin') {
        %to_copy = (
            'Are you sure?' => '',
            'Email address is missing. Please specify an email address.' => '',
            'delete this comment' => '',
            'validate this comment' => '',
        );
    }
    else {
        %to_copy = ();
    }

    my $file_content = '';
    my $copy_content = '';

    open(my $ifh, '<'.$language_dir.'/'.$file_code.'.lang.php');
    while (my $line = <$ifh>) {
        if ($line =~ m/^\$lang\['(.*)'\] \s* =/x) {
            if (defined $remove_keys{$file_code}{$1}) {
                next;
            }
            elsif (defined $ignore_keys{$1}) {
                $file_content.= $line;
            }
            elsif (defined $to_copy{$1}) {
                $append_to_common.= $line;
            }
            elsif (defined $replace_by{$1}) {
                my $search = quotemeta($1);
                my $replace = $replace_by{$1};

                $line =~ s{$search}{$replace};

                if (defined $to_copy{$replace}) {
                    $append_to_common.= $line;
                }
                else {
                    $file_content.= $line;
                }
            }
            else {
                $file_content.= $line;
            }
        }
        elsif ($line =~ m/^?>/) {
            if ('common' eq $file_code) {
                $file_content.= $append_to_common;
            }
            $file_content.= $line;
        }
        else {
            $file_content.= $line;
        }
    }
    close($ifh);

    open(my $ofh, '>'.$language_dir.'/'.$file_code.'.lang.php');
    print {$ofh} $file_content;
    close($ofh);
}

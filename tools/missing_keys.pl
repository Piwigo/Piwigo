#!/usr/bin/perl

use strict;
use warnings;

use File::Find;

our %used_keys = ();
our %registered_keys = ();

my $piwigo_dir = $ARGV[0]; # '/home/pierrick/public_html/piwigo/dev/trunk';
my $type = $ARGV[1];       # common, admin, install, upgrade

find(\&used_keys, $piwigo_dir);
load_registered_keys($type);

foreach my $key (sort keys %used_keys) {
    # print "{".$key."}", ' is used', "\n";

    if (not defined $registered_keys{$key}) {
        # print "{".$key."}", ' is missing', "\n";
        print '$lang[\''.$key.'\'] = \''.$key.'\';', "\n";
    }
}

# foreach my $key (sort keys %registered_keys) {
#     if (not defined $used_keys{$key}) {
#         print "{".$key."}", ' is not used anywhere', "\n";
#     }
# }

sub used_keys {
    if ($File::Find::name !~ m/(tpl|php)$/) {
        return 0;
    }

    if ($File::Find::name =~ m{/(plugins|language|_data)/}) {
        return 0;
    }

    if ('upgrade' eq $type) {
        if ($File::Find::name !~ m{upgrade\.(tpl|php)$}) {
            return 0;
        }
    }

    if ('install' eq $type) {
        if ($File::Find::name =~ m{upgrade\.(tpl|php)$}) {
            return 0;
        }
        if ($File::Find::name !~ m{/install(\.tpl|\.php|/)}) {
            return 0;
        }
    }

    if ('admin' eq $type) {
        if ($File::Find::name =~ m{upgrade\.(tpl|php)$}) {
            return 0;
        }
        if ($File::Find::name =~ m{/install(\.tpl|\.php|/)}) {
            return 0;
        }

        my $is_admin = 0;

        if ($File::Find::name =~ m{themes/default/template/mail}) {
            $is_admin = 1;
        }
        if ($File::Find::name =~ m{/admin/}) {
            $is_admin = 1;
        }
        if ($File::Find::name =~ m{/admin\.php$}) {
            $is_admin = 1;
        }

        if (not $is_admin) {
            return 0;
        }
    }

    if ('common' eq $type) {
        if ($File::Find::name =~ m{upgrade\.(tpl|php)$}) {
            return 0;
        }
        if ($File::Find::name =~ m{/install(\.tpl|\.php|/)}) {
            return 0;
        }
        if ($File::Find::name =~ m{/admin(/|\.php)} or $File::Find::name =~ m{themes/default/template/mail}) {
            return 0;
        }
    }

    if (-f) {
        open(my $fhi, '<', $File::Find::name);
        while (<$fhi>) {
            if ($File::Find::name =~ m/tpl$/) {
                while (m/\{(['"])(.+?)\1\|\@translate/g) {
                    $used_keys{$2}++;
                }
            }

            if ($File::Find::name =~ m/php$/) {
                while (m/l10n \s* \( \s* (['"]) (.+?) \1 \s* \)/xg) {
                    $used_keys{$2}++;
                }

                while (m/l10n_args \s* \( \s* (['"]) (.+?) \1 \s* ,/xg) {
                    $used_keys{$2}++;
                }

                while (m/l10n_dec \s* \( \s* (['"]) (.+?) \1 \s* ,\s* (['"]) (.+?) \3 \s* ,/xg) {
                    $used_keys{$2}++;
                    $used_keys{$4}++;
                }
            }
        }
    }
}

sub load_registered_keys {
    my ($type) = @_;

    my %files_for_type = (
        common  => [qw/common/],
        admin   => [qw/common admin/],
        install => [qw/common admin install/],
        upgrade => [qw/common admin install upgrade/],
    );

    foreach my $file_code (@{$files_for_type{$type}}) {
        my $filepath = $piwigo_dir.'/language/en_UK/'.$file_code.'.lang.php';

        open(my $fhi, '<', $filepath);
        while (<$fhi>) {
            if (m/\$lang\[ \s* (['"]) (.+?) \1 \s* \]/x) {
                $registered_keys{$2}++;
            }
        }
    }
}

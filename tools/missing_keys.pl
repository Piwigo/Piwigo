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

my %ignore_keys = (
    '%d new image' => 1,
    '%d new images' => 1,
    '%d category updated' => 1,
    '%d categories updated' => 1,
    '%d new comment' => 1,
    '%d new comments' => 1,
    '%d comment to validate' => 1,
    '%d comments to validate' => 1,
    '%d new user' => 1,
    '%d new users' => 1,
    '%d waiting element' => 1,
    '%d waiting elements' => 1,
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
    'public' => '',
    'private' => '',
    'none' => '',
    'other' => '',
    'high' => '',
    'Waiting page: %s' => '',
    'Admin: %s' => '',
    'Manage this user comment: %s' => '',
    'Main "guest" user does not exist' => '',
    'Main "guest" user status is incorrect' => '',
    'Main "webmaster" user does not exist' => '',
    'Main "webmaster" user status is incorrect' => '',
    'Default user does not exist' => '',
    '(!) This comment requires validation' => '',
);


# foreach my $key (sort keys %registered_keys) {
#     if (not defined $used_keys{$key} and not defined $ignore_keys{$key}) {
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
            # return 0;
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
        my $big_string = '';
        open(my $fhi, '<', $File::Find::name);
        while (<$fhi>) {
            chomp;
            s{//.*$}{};
            $big_string.= $_;
        }
        close($fhi);

        while ($big_string =~ m/\{(['"])(.+?)\1\|\@translate/g) {
            $used_keys{$2}++;
        }

        while ($big_string =~ m/l10n \s* \( \s* (['"]) (.+?) \1 \s* \)/xg) {
            $used_keys{$2}++;
        }

        while ($big_string =~ m/l10n_args \s* \( \s* (['"]) (.+?) \1 \s* ,/xg) {
            $used_keys{$2}++;
        }

        while ($big_string =~ m/l10n_dec \s* \( \s* (['"]) (.+?) \1 \s* ,\s* (['"]) (.+?) \3 \s* ,/xg) {
            $used_keys{$2}++;
            $used_keys{$4}++;
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

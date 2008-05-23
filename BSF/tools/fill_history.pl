#!/usr/bin/perl

use warnings;
use strict;
use Getopt::Long;
use DBI;
use File::Basename;
use Time::Local;
use List::Util qw/shuffle min/;

my %opt;
GetOptions(
    \%opt,
    qw/dbname=s dbuser=s dbpass=s prefix=s
       total=i start_date=s end_date=s
       help/
   );

if (defined($opt{help}))
{
  print <<FIN;

Fill the user comments table of Piwigo.

Usage: pwg_fill_comments.pl --dbname=<database_name>
                            --dbuser=<username>
                            --dbpass=<password>
                            --tagfile=<tags filename>
                            [--prefix=<tables prefix>]
                            [--help]

--dbname, --dbuser and --dbpass are connexion parameters.

--tagfile

--prefix : determines the prefix for your table names.

--help : show this help

FIN

  exit(0);
}

my $usage = "\n\n".basename($0)." --help for help\n\n";

foreach my $option (qw/dbname dbuser dbpass start_date end_date/) {
  if (not exists $opt{$option}) {
    die 'Error: '.$option.' is a mandatory option', $usage;
  }
}

$opt{prefix} = 'piwigo_' if (not defined $opt{prefix});
my $dbh = DBI->connect(
    'DBI:mysql:'.$opt{dbname},
    $opt{dbuser},
    $opt{dbpass}
);

my $query;
my $sth;


# retrieve all available users
$query = '
SELECT id
  FROM '.$opt{prefix}.'users
';
my @user_ids = keys %{ $dbh->selectall_hashref($query, 'id') };

# set a list of IP addresses for each users
my %user_IPs = ();
foreach my $user_id (@user_ids) {
    for (1 .. 1 + int rand 5) {
        push(
            @{ $user_IPs{$user_id} },
            join(
                '.',
                map {1 + int rand 255} 1..4
            )
        );
    }
}

# use Data::Dumper; print Dumper(\%user_IPs); exit();

# start and end dates
my ($year,$month,$day,$hour,$min,$sec)
     = ($opt{start_date} =~ m/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/);
my $start_unixtime = timelocal(0,0,0,$day,$month-1,$year);

($year,$month,$day,$hour,$min,$sec)
     = ($opt{end_date} =~ m/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/);
my $end_unixtime = timelocal(0,0,0,$day,$month-1,$year);

# "tags from image" and "images from tag"
$query = '
SELECT image_id, tag_id
  FROM '.$opt{prefix}.'image_tag
';
my %image_tags = ();
my %tag_images = ();
my %related_tag_of = ();
my @tags = ();
$sth = $dbh->prepare($query);
$sth->execute();
while (my $row = $sth->fetchrow_hashref()) {
    push(
        @{$image_tags{$row->{image_id}}},
        $row->{tag_id}
    );

    push(
        @{$tag_images{$row->{tag_id}}},
        $row->{image_id}
    );

    push (
        @tags,
        $row->{tag_id}
    );
}

# foreach my $tag_id (keys %tag_images) {
#     printf(
#         "tag %5u: %5u images\n",
#         $tag_id,
#         scalar @{$tag_images{$tag_id}}
#     );
# }
# exit();

# use Data::Dumper; print Dumper(\%tag_images); exit();

# categories from image_id
$query = '
SELECT image_id, category_id
  FROM '.$opt{prefix}.'image_category
';
my %image_categories = ();
my %category_images =();
my %categories = ();
$sth = $dbh->prepare($query);
$sth->execute();
while (my $row = $sth->fetchrow_hashref()) {
    push(
        @{$image_categories{$row->{image_id}}},
        $row->{category_id}
    );

    push(
        @{$category_images{$row->{category_id}}},
        $row->{image_id}
    );

    $categories{ $row->{category_id} }++;
}

my @images = keys %image_categories;
my @categories = keys %categories;

# use Data::Dumper;
# print Dumper(\%image_categories);

my @sections = (
    'categories',
#     'tags',
#     'search',
#     'list',
#     'favorites',
#     'most_visited',
#     'best_rated',
#     'recent_pics',
#     'recent_cats',
);

my @inserts = ();

USER : foreach my $user_id (@user_ids) {
    print 'user_id: ', $user_id, "\n";

    my $current_unixtime = $start_unixtime;
    my @IPs = @{ $user_IPs{$user_id} };

    VISIT : foreach my $visit_num (1..100_000) {
        print 'visit: ', $visit_num, "\n";
        my @temp_inserts = ();
        my $IP = (@IPs)[int rand @IPs];
        my $current_page = 0;
        my $visit_size = 10 + int rand 90;
        $current_unixtime+= 86_400 + int rand(86_400 * 30);

        my $section = $sections[int rand scalar @sections];
        # print 'section: ', $section, "\n";

        push(
            @temp_inserts,
            {
                section => $section,
            }
        );

        if ($section eq 'categories') {
            CATEGORY : foreach my $category_id (
                (shuffle @categories)[1..int rand scalar @categories]
            ) {
                # print 'category: ', $category_id, "\n";
                push(
                    @temp_inserts,
                    {
                        category_id => $category_id,
                    }
                );

                my @images = @{$category_images{$category_id}};
                IMAGE : foreach my $image_id (
                    (shuffle @images)[1..min(10, scalar @images)]
                ) {
                    push(
                        @temp_inserts,
                        {
                            category_id => $category_id,
                            image_id => $image_id,
                        }
                    );
                }
            }
        }

        if ($section eq 'tags') {
            # TODO
        }

        # transfert @temp_inserts to @inserts
        print 'temp_insert size: ', scalar @temp_inserts, "\n";
        foreach my $temp_insert (@temp_inserts) {
            $current_unixtime+= 5 + int rand 25;
            next VISIT if ++$current_page == $visit_size;
            last VISIT if $current_unixtime >= $end_unixtime;

            my $date = unixtime_to_mysqldate($current_unixtime);
            my $time = unixtime_to_mysqltime($current_unixtime);

            my ($year, $month, $day) = split '-', $date;
            my ($hour) = split ':', $time;

            $temp_insert->{date} = $date;
            $temp_insert->{time} = $time;
            $temp_insert->{year} = $year;
            $temp_insert->{month} = $month;
            $temp_insert->{day} = $day;
            $temp_insert->{hour} = $hour;
            $temp_insert->{IP} = $IP;
            $temp_insert->{section} = $section;
            $temp_insert->{user_id} = $user_id;

            push(@inserts, $temp_insert);
        }
    }
}

@inserts = sort {
    $a->{date} cmp $b->{date}
    or $a->{time} cmp $b->{time}
} @inserts;

if (scalar @inserts) {
    my @columns =
        qw/
              date time year month day hour
              user_id IP
              section category_id image_id
          /;

    my $question_marks_string = '(';
    $question_marks_string.= join(
        ',',
        map {'?'} @columns
    );
    $question_marks_string.= ')';

    my $query = '
INSERT INTO '.$opt{prefix}.'history
  ('.join(', ', @columns).')
  VALUES
';
    $query.= join(
        ',',
        map {$question_marks_string} (1 .. scalar @inserts)
    );
    $query.= '
';

    # print $query, "\n";

    my @values = ();

    foreach my $insert (@inserts) {
        push(
            @values,
            map {
                $insert->{$_}
            } @columns
        );
    }

    $sth = $dbh->prepare($query);
    $sth->execute(@values)
        or die 'cannot execute insert query';
}

sub unixtime_to_mysqldate {
    my ($unixtime) = @_;

    ($sec,$min,$hour,$day,$month,$year) = localtime($unixtime);

    return sprintf(
        '%d-%02d-%02d',
        $year+1900,
        $month+1,
        $day,
    );
}

sub unixtime_to_mysqltime {
    my ($unixtime) = @_;

    ($sec,$min,$hour,$day,$month,$year) = localtime($unixtime);

    return sprintf(
        '%d:%d:%d',
        $hour,
        $min,
        $sec
    );
}

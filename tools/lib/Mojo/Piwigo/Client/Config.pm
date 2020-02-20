use 5.024; 
package Mojo::Piwigo::Client::Config {
    use strict;
    use warnings;
    use Try::Tiny;
    use YAML::XS ();

    use constant DEFAULT_CONFIG => "$ENV{HOME}/.config/piwigo/client.yaml";
    use constant REQUIRED_ARGS => [ qw/ url user password / ];

    sub new {
        my $class = shift;
        my %opts = @_%2 ? $_[0]->%* : @_;

        my $cfg = try {
            YAML::XS::LoadFile( delete $opts{config} // DEFAULT_CONFIG );
        } catch {
            {}
        };

        %opts = (
            %$cfg,
            map { $_ => $opts{$_} } grep { defined $opts{$_} } REQUIRED_ARGS->@*
        );
        my @missing = grep { not defined $opts{$_} } REQUIRED_ARGS->@*;
        @missing and die "required arguments missing: " . join(", ", @missing) . "\n";

        return bless { cfg => \%opts, }, $class;
    }

    sub cfg {
        return shift->{cfg}->%*;
    }
}

1;

__END__

=head1 SYNOPSIS

    use Mojo::Piwigo::Client;
    use Mojo::Piwigo::Client::Config;

    # Instantiate a client
    my $config = Mojo::Piwigo::Client::Config->new( \%options );
    my $cl = Mojo::Piwigo::Client->new( $config->cfg );

=head1 DESCRIPTION

This is a trivial config file class to simplify passing constructor
arguments to L<Mojo::Piwigo::Client>. Its constructor takes a hash (inline or
as a reference) that may contain the keys C<url>, C<user>, C<password>,
and C<config>. If C<config> is defined, it specifies the path to a
YAML config file to load; it defaults to C<$HOME/.config/piwigo/client.yaml>.

The config file must contain a hash with any of the first three keys, e.g.

    ---
    url: https://my.piwigo.org/
    user: itsme
    password: seeeecret!

Any values in the constructor arguments overwrite the defaults in the config file.
Unless all three required arguments are defined in the union of the config file
and the constructor arguments, an exception will be thrown.

=over 4

url user password parent

=back

=head1 ATTRIBUTES

=head2 cfg

The only attribute, contains a hash of the keys merged constructor arguments
and the config file. You can pass this directly to L<Mojo::Piwigo::Client/new>,
see L</SYNOPSIS>.

=head1 AUTHOR

Matthias Bethke <mbethke@cpan.org>

=cut


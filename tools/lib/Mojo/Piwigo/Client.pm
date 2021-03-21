# ABSTRACT: Client for Piwigo's Web Service API
use 5.024; 

package Mojo::Piwigo::Client {
    use version 0.77; our $VERSION = version->declare("v0.0.1");
    use Mojo::Base -base;
    use Mojo::UserAgent;
    use Mojo::Exception;
    use Path::Tiny;
    use POSIX 'ceil';
    use namespace::clean;

    has [qw/ user password /];
    has chunk_size => 1_024_476;
    has url => sub {
        Mojo::Piwigo::Client::Exception::Args->raise( "required attribute 'url' missing" );
    };
    has ua => sub {
        my $ua = Mojo::UserAgent->new;
        $ua->transactor->name( __PACKAGE__ . $VERSION );
        return $ua;
    };
    has endpoint => sub {
        my $url = Mojo::URL->new( shift->url );
        $url->path->merge( 'ws.php' );
        $url->query( 'format=json' );
        return $url;
    };
    has [qw/ _token /];
    has _needs_token => sub {
        shift->_token // Mojo::Piwigo::Client::Exception::Args->raise( "session token not set, call login() first" );
    };

    sub login {
        my ($self) = @_;
        my $user = $self->user;
        my $pass = $self->password;
        defined $user and defined $pass
            or Mojo::Piwigo::Client::Exception::Args->raise( "can't call login() without user and password set" );

        $self->call( 'pwg.session.login', username => $user, password => $pass );

        my $res = $self->status;
        if( $res->{status} eq 'guest' ) {
            Mojo::Piwigo::Client::Exception::Args->raise( "login failed, wrong username or password" );
        }
        $self->_token( $res->{pwg_token} );
        return $self;
    }

    sub logout {
        my ($self) = @_;
        $self->call_res( 'pwg.session.logout' );
        $self->_token( undef );
        return $self;
    }

    sub status {
        return shift->call_res( 'pwg.session.getStatus' );
    }

    sub add_category {
        my ($self, $name) = (shift, shift);
        return $self->call_res( 'pwg.categories.add', @_, name =>  $name)->{id};
    }

    sub categories {
        my ($self, %args) = @_;
        my $res = $self->call_res( 'pwg.categories.getList', %args );
        # Fix API brain damage
        return $args{tree_output} ? $res : $res->{categories};
    }

    sub upload {
        my ($self, $filename, $albumid, $progress_cb, %args) = @_;
        my $chunk_size = $self->chunk_size;

        my $path = path( $filename );
        $path->is_file or Mojo::Piwigo::Client::Exception::Args->raise(
            "$path is not a plain file"
        );

        my $size = -s $path or Mojo::Piwigo::Client::Exception::Args->raise(
            "file $path is empty"
        );

        my $fh = $path->openr_raw or Mojo::Piwigo::Client::Exception::Args->raise(
            "error opening $path for reading: $!"
        );

        my $nchunks = ceil( $size / $chunk_size ); 
        my $chunk = 0;
        my $read_total = 0;
        while( $chunk < $nchunks ) {
            $progress_cb->( $filename, $albumid, $chunk, $nchunks ) if $progress_cb;

            my $read = sysread( $fh, my $filedata, $chunk_size, 0) // Mojo::Piwigo::Client::Exception::Request->raise(
                "read error on $path: $!"
            );
            $read_total += $read;

            my $tx = $self->call( 'pwg.images.upload',
                %args,
                chunk => $chunk,
                chunks => $nchunks,
                category => $albumid,
                file => { content => $filedata },
                name => $path->basename,
                $self->_token_args,
            );
            $tx->res->code == 200 or Mojo::Piwigo::Client::Exception::Request->raise(
                "server error: " . $tx->res->code . ' ' . $tx->res->message
            );
            $chunk++;
        }

        $read_total == $size or Mojo::Piwigo::Client::Exception::Request->raise(
            "short read on $path: expected $size bytes but read only $read_total"
        );

        return $self;
    }

    sub get_images {
        return shift->call_res( 'pwg.categories.getImages', @_ );
    }

    sub call_res {
        my $self = shift;
        my $tx = $self->call( @_ );
        my $result = $tx->result->json or Mojo::Piwigo::Client::Exception::Request->raise(
            "missing result from $_[0], server said: " . $tx->res->code . ' ' . $tx->res->message
        );
        $result->{stat} eq 'ok' or Mojo::Piwigo::Client::Exception::Request->raise(
            "server returned failure: $result->{err} $result->{message}"
        );
        return $result->{result};
    }

    sub call {
        my ($self, $method, %formargs) = @_;
        return $self->ua->post( $self->endpoint, form => { %formargs, method => $method } );
    }

    sub call_p {
        my ($self, $method, %formargs) = @_;
        return $self->ua->post_p( $self->endpoint, form => { %formargs, method => $method } );
    }

    # If login token is present, return pwg_token => $token; otherwise an empty list
    sub _token_args {
        my $token = shift->_token;
        return ( pwg_token => $token ) x !!$token;
    }
}

package Mojo::Piwigo::Client::Exception::Args { use Mojo::Base 'Mojo::Exception'; }
package Mojo::Piwigo::Client::Exception::Request { use Mojo::Base 'Mojo::Exception'; }

1;
__END__

=head1 SYNOPSIS

    use Mojo::Piwigo::Client;

    # Instantiate a client
    my $cl = Mojo::Piwigo::Client->new(
        url => 'https://my.piwigo.org/',
        user => 'username',
        password => 'secret'
    );
    # Adjust some things about the user agent
    $cl->ua->request_timeout( 20 );
    $cl->login;
    # Upload local files to album #42
    $cl->upload( $_, 42 ) for qw/ foo.jpg bar.jpg /;
    $cl->logout;

=head1 DESCRIPTION

This module provides a Perl interface to Piwigo's web service. So far, only a
few API methods are supported with individual Perl methods, but a
general-purpose method call is available to call every possible web service
method.

All methods return C<$self> to allow method chaining unless otherwise
specified. All methods also throw an exception if there should be a server
error either on the HTTP or the web service level.

=head1 ATTRIBUTES

=head2 url

The URL for your Piwigo server. This is the only attribute required for basic operation.

=head2 user

User name on your Piwigo server

=head2 password

Password for C<user>.

=head2 chunk_size

When uploading images, use chunks of this size so as not to run into potential
web server limitations with big pictures. Defaults to 1 MB.

=head2 ua

A L<Mojo::UserAgent> object that will usually be built for you.

=head2 endpoint

A L<Mojo::URL> object describing the WS query endpoint. Built from C<url> and
best treated as read-only.

=head1 METHODS

Generally, methods take required arguments as positional parameters and accept
any optional ones as additional hash-style arguments. In keeping with L<Mojo>
conventions, the constructor wants I<all> hash-style arguments though.

=head2 new

    $cl = Mojo::Piwigo::Client->new( url => 'https://...', user => $u, password => $p );

The only required argument for the constructor (unless you want to set it
later, but why would you?) is C<url>. However, you'll likely want to add
C<user> and C<password> because many methods require a login.

=head2 login

    $cl->login;

Log in to the server. Requires C<user> and C<password> to be set and will throw
an exception if they are not.

=head2 logout

    $cl->logout;

Terminate the current session.

=head2 status

    $s = $cl->status;

Returns a reference to a hash of various tidbits about your session, see
C<pwg.session.getStatus>.

=head2 upload

    $cl->upload( '$file.jpg', 42 );
    $cl->upload( '$file.jpg', 42,
        sub { printf("Uploading chunk %d/%d of file %s to album #%d\n", @_[2,3,0,1]);
        }
    );
    $cl->upload( '$file.jpg', 42, undef, level => 8 );

Upload an image to an album specified by ID. You'll usually want to call
L<login> first unless your server is very permissive.

The two requires arguments are a locally accessible image file name and an
album ID. A third argument may be a callback that will be called once for every
chunk uploaded. Further arguments to C<pwg.images.upload> may be specified
hash-style.

=head2 add_category

    $id = $cl->add_category( "My album" );
    $id = $cl->add_category( "My album",
        parent => 42,
        comment => "Nothing to see here"
    );

Add a category and return its numeric ID.

=head2 categories

    $catlist = $cl->categories;
    $catlist = $cl->categories(
        cat_id => 42,
        recursive => 1,
    );

Returns a reference to a list of categories, each represented as a hash.

=head2 get_images

    $imgs = $cl->get_images;
    $imgs = $cl->get_images( cat_id => 42 );

Get a list of images, optionally restricted to a certain category.

=head2 call_res

    $result = $cl->call_res( 'pwg.categories.setInfo',
        category_id => 42,
        comment => 'There are many albums, but this one is mine!'
    );

Call a method and return its result if the server returned success, otherwise
an exception is thrown.

=head2 call

    $tx = $cl->call( $method, %args );
    $tx->res->code == 200 or die "b0rk: " . $tx->res->message;

Call a method and return a L<Mojo::Transaction::HTTP> object representing the
result. Use this if you want to look at the raw HTTP result codes and such.

=head2 call_p

An asnynchronous version of L</call>, returns a L<Mojo::Promise>. Experimental.

=head1 BUGS

Probably. At the very least, exception handling needs improving and documentation.

=head1 AUTHOR

Matthias Bethke <mbethke@cpan.org>

=head1 SEE ALSO

Mojo::Piwigo::Client is based on the L<Mojo> framework, see there for details.

=cut


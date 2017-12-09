FROM php:7.1-apache

ARG PIWIGO_VERSION=2.9.2
ARG PIWIGO_SHA512=95714ea12d67e20e00bc03b221e5bb597d7be133fe676c3617c60845c55d73ef000a81f6d43289179193f826000748ba908b29cac60bcb70572ee96d9b3e44f9

# Install required PHP extensions
RUN buildDeps=" \
    libjpeg62-turbo-dev \
    libpng-dev" \
  && apt-get -qq update \
  && apt-get install -qq $buildDeps \
    imagemagick \
  && docker-php-ext-configure gd --with-png-dir=/usr/include --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install -j$(nproc) \
    exif \
    gd \
    mysqli \
  && apt-mark manual \
    libjpeg62-turbo \
    libpng12-0 \
  && apt-get purge -y --auto-remove $buildDeps \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

VOLUME /var/www/html

ENV PIWIGO_VERSION $PIWIGO_VERSION
ENV PIWIGO_SHA512 $PIWIGO_SHA512

RUN set -eu; \
	curl -o piwigo.tar.gz -SL "https://github.com/Piwigo/Piwigo/archive/${PIWIGO_VERSION}.tar.gz"; \
	echo "$PIWIGO_SHA512 *piwigo.tar.gz" | sha512sum -c -; \
	# upstream tarball include ./Piwigo-${PIWIGO_VERSION}/
	tar -xzf piwigo.tar.gz -C /usr/src/; \
	mv /usr/src/Piwigo-${PIWIGO_VERSION} /usr/src/piwigo; \
	rm piwigo.tar.gz; \
	# Does not exist in tarball but is required
	chown -R www-data:www-data /usr/src/piwigo

COPY docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]

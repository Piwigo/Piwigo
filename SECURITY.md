# Piwigo Security Policy

The Piwigo team and community take security bugs seriously. We appreciate your efforts
to responsibly disclose your findings.

## Supported Versions

Security updates will typically only be applied to the latest release. Exceptionally,
we may release a new version of an old branch but that won't be the standard way we process.

## Reporting a Vulnerability

**Please contact us at [security@piwigo.org](mailto:security@piwigo.org) when you have
discovered a potential security issue.** At a minimum, your report should include the following:

- version of Piwigo, version of PHP, version of MySQL/MariaDB
- vulnerability description
- reproduction steps

You will receive a response from us within 72 hours. If the issue is confirmed we will
then work on fixing it and release a new fixed version of Piwigo, following these steps:

- Confirm the problem and determine the affected versions.
- Audit code to find any potential similar problems.
- Prepare a fix for `master` branch and backport it on the current stable branch.
- Release a new version of Piwigo on its current stable branch as fast as possible, historically within a few days.

## Responsible Disclosure

1. Confirm that the vulnerability applies to a current version and is reproducible.
2. First share the vulnerability details with us so that users are not put at risk.
3. Wait before publishing details until everyone has had a chance to update.
4. Respect the privacy of others.

*Avoid activities that disrupt, degrade, or interrupt our services or compromise other
users' data, such as spam, brute force attacks, denial of service attacks, and malicious file distribution.*

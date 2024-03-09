# Potential security issues
1. [x] Private project code leaked to external AI services
   - Host all AI models locally/on a private server.
   - Build the ML LLM service as a docker container and run it alongside the main app.
2. [x] User access control
   - Implement user login functionality and allow access to imported repos only to user that imported them
3. [x] Source code security issues.
   - Implement SAST tool to scan code in CI
     - [x] Implement [Snyk](https://snyk.io) monitoring tool.
     - [x] Implement [Semgrep](https://github.com/semgrep/semgrep) open source SAST.
4. [ ] Review [OWASP TOP 10](https://owasp.org/www-project-top-ten/)
    - [x] [A01:2021 – Broken Access Control](https://owasp.org/Top10/A01_2021-Broken_Access_Control/)
        - [x] Make sure non admin users cant access the admin configuration. 
        - [x] Make sure users can't access private repositories and commits from other users.
    - [ ]  [A02:2021 – Cryptographic Failures](https://owasp.org/Top10/A02_2021-Cryptographic_Failures/)
      - [ ] Verify that the data is not being transferred in plain text.
      - [ ] Implement https endpoints.
   - [x] [A03:2021 – Injection](https://owasp.org/Top10/A03_2021-Injection/)
     - [x] Make sure all the user data is validated, filtered, or sanitized by the application. 
   - [ ] [A04:2021 – Insecure Design](https://owasp.org/Top10/A04_2021-Insecure_Design/)
     - [x] Implement security analysis in CI/tolling.
     - [ ] Learn more about secure design.
   - [ ] [A05_2021-Security_Misconfiguration](https://owasp.org/Top10/A05_2021-Security_Misconfiguration/)
     - [x] Security hardening based on SAST analysis.
   - [ ] [A06:2021 – Vulnerable and Outdated Components](https://owasp.org/Top10/A06_2021-Vulnerable_and_Outdated_Components/)
     - [x] Use dependency bots to keep libraries up to date (dependabot)
     - [x] Check for some PHP/Laravel specific dependency bots.
     - [ ] Remove unused dependencies if any.
   - [ ] [A07:2021 – Identification and Authentication Failures](https://owasp.org/Top10/A07_2021-Identification_and_Authentication_Failures/)
     - [x] Consider only using GitHub and other OAuth providers for login.
   - [ ] [A08:2021 – Software and Data Integrity Failures](https://owasp.org/Top10/A08_2021-Software_and_Data_Integrity_Failures/)
     - [x] software supply chain security tool - Snyk to analyse dependencies.
   - [ ] [A09:2021 – Security Logging and Monitoring Failures](https://owasp.org/Top10/A09_2021-Security_Logging_and_Monitoring_Failures/)
     - [ ] Improve logging and monitoring.
     - [x] Explore penetration testing and scans by dynamic application security testing (DAST) tools (such as OWASP ZAP) do not trigger alerts.
   - [x] [A10:2021 – Server-Side Request Forgery (SSRF)](https://owasp.org/Top10/A10_2021-Server-Side_Request_Forgery/)
     - [x] Sanitize and validate all client-supplied input data
5.[ ] Fix security issues found by SAST:
   - [ ] Dockerfile
   ❯❯❱ dockerfile.security.missing-user.missing-user
   By not specifying a USER, a program in the container may run as 'root'. This is a security hazard.
   If an attacker can control a process running as root, they may have control over the container.
   Ensure that the last USER in a Dockerfile is a USER other than 'root'.
   Details: https://sg.run/Gbvn

           ▶▶┆ Autofix ▶ USER non-rootCMD php artisan serve --host 0.0.0.0
           38┆ CMD php artisan serve --host 0.0.0.0

   - [x] app/Services/Git/GitRepositoryService.php
   ❯❯❱ php.lang.security.exec-use.exec-use
   Executing non-constant commands. This can lead to command injection.
   Details: https://sg.run/5Q1j

          113┆ exec($command, $output);
            ⋮┆----------------------------------------
          119┆ exec("cd $this->dirName && git --no-pager show $commitHash", $output);
            ⋮┆----------------------------------------
          130┆ system('rm -rf '.$dir);

   - [x] docker-compose.yml
   ❯❱ yaml.docker-compose.security.no-new-privileges.no-new-privileges
   Service 'app' allows for privilege escalation via setuid or setgid binaries. Add 'no-new-
   privileges:true' in 'security_opt' to prevent this.
   Details: https://sg.run/0n8q

            4┆ app:

   - [x] ❯❱ yaml.docker-compose.security.writable-filesystem-service.writable-filesystem-service
   Service 'app' is running with a writable root filesystem. This may allow malicious applications to
   download and run additional payloads, or modify container files. If an application inside a
   container has to save something temporarily consider using a tmpfs. Add 'read_only: true' to this
   service to prevent this.
   Details: https://sg.run/e4JE

            4┆ app:

   - [x] resources/views/livewire/layout/navigation.blade.php
   ❯❱ php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
   The `redirect()` method does not check its destination in any way. If you redirect to a URL provided
   by end-users, your application may be open to the unvalidated redirects security vulnerability.
   Consider using literal values or an allowlist to validate URLs.
   Details: https://sg.run/4ey5

           15┆ $this->redirect('/', navigate: true);

   - [x] resources/views/livewire/pages/auth/register.blade.php
   ❯❱ php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
   The `redirect()` method does not check its destination in any way. If you redirect to a URL provided
   by end-users, your application may be open to the unvalidated redirects security vulnerability.
   Consider using literal values or an allowlist to validate URLs.
   Details: https://sg.run/4ey5

           36┆ $this->redirect(RouteServiceProvider::HOME, navigate: true);

   - [x] resources/views/livewire/pages/auth/verify-email.blade.php
   ❯❱ php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
   The `redirect()` method does not check its destination in any way. If you redirect to a URL provided
   by end-users, your application may be open to the unvalidated redirects security vulnerability.
   Consider using literal values or an allowlist to validate URLs.
   Details: https://sg.run/4ey5

           35┆ $this->redirect('/', navigate: true);

   - [x] resources/views/livewire/profile/delete-user-form.blade.php
   ❯❱ php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
   The `redirect()` method does not check its destination in any way. If you redirect to a URL provided
   by end-users, your application may be open to the unvalidated redirects security vulnerability.
   Consider using literal values or an allowlist to validate URLs.
   Details: https://sg.run/4ey5

           22┆ $this->redirect('/', navigate: true);
6. [ ] Perform penetration testing with ZAP and fix any security issues found:
   - [x] Installed ZAP and ran automated penetration test.
   - [ ] Dive deeper into pentesting.
   - [ ] Fix any security issues found by ZAP that are high risk
   - [ ] Evaluate security issues found by ZAP that are medium to low risk.
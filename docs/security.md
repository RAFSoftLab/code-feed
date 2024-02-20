# Potential security issues
1. [ ] Private project code leaked to external AI services
   - Host all AI models locally/on a private server.
   - Build the ML LLM service as a docker container and run it alongside the main app.
   - 
2. [x] User access control
   - Implement user login functionality and allow access to imported repos only to user that imported them
   
3. [x] Source code security issues
   - Implement SAST tool to scan code in CI
     - [x] Implement [Snyk](https://snyk.io) monitoring tool.
     - [x] Implement [Semgrep](https://github.com/semgrep/semgrep) open source SAST.
4. [ ] Fix security issues:
   - [ ] Dockerfile
   ❯❯❱ dockerfile.security.missing-user.missing-user
   By not specifying a USER, a program in the container may run as 'root'. This is a security hazard.
   If an attacker can control a process running as root, they may have control over the container.
   Ensure that the last USER in a Dockerfile is a USER other than 'root'.
   Details: https://sg.run/Gbvn

           ▶▶┆ Autofix ▶ USER non-rootCMD php artisan serve --host 0.0.0.0
           38┆ CMD php artisan serve --host 0.0.0.0

   - [ ] app/Services/Git/GitRepositoryService.php
   ❯❯❱ php.lang.security.exec-use.exec-use
   Executing non-constant commands. This can lead to command injection.
   Details: https://sg.run/5Q1j

          113┆ exec($command, $output);
            ⋮┆----------------------------------------
          119┆ exec("cd $this->dirName && git --no-pager show $commitHash", $output);
            ⋮┆----------------------------------------
          130┆ system('rm -rf '.$dir);

   - [ ] docker-compose.yml
   ❯❱ yaml.docker-compose.security.no-new-privileges.no-new-privileges
   Service 'app' allows for privilege escalation via setuid or setgid binaries. Add 'no-new-
   privileges:true' in 'security_opt' to prevent this.
   Details: https://sg.run/0n8q

            4┆ app:

   - [ ] ❯❱ yaml.docker-compose.security.writable-filesystem-service.writable-filesystem-service
   Service 'app' is running with a writable root filesystem. This may allow malicious applications to
   download and run additional payloads, or modify container files. If an application inside a
   container has to save something temporarily consider using a tmpfs. Add 'read_only: true' to this
   service to prevent this.
   Details: https://sg.run/e4JE

            4┆ app:

   - [ ] resources/views/livewire/layout/navigation.blade.php
   ❯❱ php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
   The `redirect()` method does not check its destination in any way. If you redirect to a URL provided
   by end-users, your application may be open to the unvalidated redirects security vulnerability.
   Consider using literal values or an allowlist to validate URLs.
   Details: https://sg.run/4ey5

           15┆ $this->redirect('/', navigate: true);

   - [ ] resources/views/livewire/pages/auth/register.blade.php
   ❯❱ php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
   The `redirect()` method does not check its destination in any way. If you redirect to a URL provided
   by end-users, your application may be open to the unvalidated redirects security vulnerability.
   Consider using literal values or an allowlist to validate URLs.
   Details: https://sg.run/4ey5

           36┆ $this->redirect(RouteServiceProvider::HOME, navigate: true);

   - [ ] resources/views/livewire/pages/auth/verify-email.blade.php
   ❯❱ php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
   The `redirect()` method does not check its destination in any way. If you redirect to a URL provided
   by end-users, your application may be open to the unvalidated redirects security vulnerability.
   Consider using literal values or an allowlist to validate URLs.
   Details: https://sg.run/4ey5

           35┆ $this->redirect('/', navigate: true);

   - [ ] resources/views/livewire/profile/delete-user-form.blade.php
   ❯❱ php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
   The `redirect()` method does not check its destination in any way. If you redirect to a URL provided
   by end-users, your application may be open to the unvalidated redirects security vulnerability.
   Consider using literal values or an allowlist to validate URLs.
   Details: https://sg.run/4ey5

           22┆ $this->redirect('/', navigate: true);


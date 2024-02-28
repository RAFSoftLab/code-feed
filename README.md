[![Tests](https://github.com/RAFSoftLab/code-feed/actions/workflows/ci.yml/badge.svg)](https://github.com/RAFSoftLab/code-feed/actions/workflows/ci.yml)
# Code-Feed
![Feed Idea](docs/feed-idea.png)

Code Feed is a tool that helps teams that are want to use non-blocking pull request reviews.


# Running locally
## Requirements
1. PHP 8+
2. Composer 2.6+
3. Docker
##  Build container
Set up project environment:
```bash
cp .env.example .env
```
When running for the first time, obtain an API key for [Google Gemini model](https://support.gemini.com/hc/en-us/articles/360031080191-How-do-I-create-an-API-key) and paste it into .env file.
as well as the GitHub's application secrets.

Build and start docker containers (customize .env for laradock if needed):
```bash
cd laradock
cp .env.example .env
docker-compose up -d nginx
```
Visit localhost in your browser to view the app.

## Optional - refresh web page automatically
```bash
npm run dev
```
When editing the code, it will automatically refresh the web page.
## Running without docker
```bash
sudo chmod -R 777 storage
```
First time to fix the storage permissions.
```bash
php artisan serve
```
to run the server.
```bash
npm run dev
```
When editing the code, it will automatically refresh the web page.
3
If working with WSL and using SQLite db, Datagrip or intellij won't be able too access the db. Copy it to windows and
```bash
ln -sf /mnt/c/work/database.sqlite database.sqlite
```
Running background tasks(importing repositories):
```bash
php artisan queue:work --timeout=0

```
Running the automated repository updates in the background: add the following to cron
```
* * * * * cd /home/sfodor/code/code-feed/ && php artisan schedule:run >> /dev/null 2>&1
```
Replace sfodor with your user name and correct path to code-feed.
## Documentation
 - [Functional Requirements](docs/requirements.md)
 - [Diagrams](docs/diagrams.md)
 - [Prompt engineering](docs/prompt-engineering.md)
 - [Security considerations](docs/security.md)
 - [Kanban Board](https://github.com/orgs/RAFSoftLab/projects/5)

## License
MIT


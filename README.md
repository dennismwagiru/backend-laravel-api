News Aggregator API Developer Task
==============
![CI Workflow](https://github.com/dennismwagiru/barnacle-news-api/actions/workflows/checks.yml/badge.svg "Workflow Badge")


Writing a News Aggregator Api using Laravel, in PHP8.2.

### Background


### Tech-stack
#### (Development Environment)
* [WSL 2](https://docs.microsoft.com/en-us/windows/wsl/install) - a compatibility layer for running Linux binary executables natively on Windows 10, Windows 11, and Windows Server 2019.
* [Ubuntu](https://ubuntu.com/wsl) - allows access to the Linux terminal on Windows, develop cross-platform applications, and manage IT infrastructure without leaving Windows.
* [PHP 8.2](https://www.php.net/releases/8.2/en.php) - an interpreted high-level general-purpose programming language
* [Docker](https://www.docker.com/) - a set of platform as a service products that use OS-level virtualization to deliver software in packages called containers.
    * [Laravel](https://laravel.com/) - a web framework written in PHP
    * [MySQL](https://www.mysql.com/) - a cross platform relational database program
    * [Nginx](https://www.nginx.com/) - a web server used to serve the application

### Running Locally
* The project has been containerized with the following services included:-

  | Service    | Port |
  |------------|------|
  | APP (HTTP) | 80   |
  | MySQL      | 3306 |
  | Nginx      | 80   |

* Follow these steps for the initial setup
    1. Clone the repository
        ````bash
            git clone git@github.com:dennismwagiru/barnacle-news-api.git && cd barnacle-news-api
        ````
    2. Build and start server
        ```bash
           make install
        ```

#### Sources Implemented
- [X] News API
- [X] New York Times
- [X] The Guardian

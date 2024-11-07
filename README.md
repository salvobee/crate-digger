
# Crate-Digger

[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F22553d3f-3aaa-42c1-b032-94d2f2705ccc%3Fdate%3D1%26commit%3D1&style=plastic)](https://forge.laravel.com/servers/857944/sites/2522769)

Crate-Digger is an app for record collectors and crate-digging enthusiasts that allows them to explore, manage, and discover new tracks through integration with external platforms.

## Project Description

The app connects to your **Discogs** account to leverage its features, along with additional tools for music discovery. Key features include:

-   Exploration of personal **wantlists** with the ability to find sellers who have the most requested items.
-   Creation and management of **custom lists**.
-   Search for **similar tracks** based on user preferences.
-   Track recommendations and content suggested by sellers.

## Technology Stack

-   **Backend**: Laravel 10
-   **Authentication**: [Laravel Socialite](https://socialiteproviders.com/Discogs/)
-   **Language**: PHP
-   **Database**: MySQL / PostgreSQL
-   **External Services**: Discogs API, integration with [cosine.club](https://cosine.club/) for music recommendations.

## Main Features

### Discogs Integration

-   **OAuth 2.0 Authentication** to connect the user's Discogs account.
-   **Wantlist exploration**: View and manage items in the user's wantlist.
-   **Seller search**: Find sellers with the most items in the user's wantlist.

### Features in Development

-   **Custom list creation**.
-   **Similar track search** using external services like cosine.club.
-   **Music recommendations** from sellers.

## Installation

### Prerequisites

-   PHP >= 8.1
-   Composer
-   Laravel 10
-   MySQL / PostgreSQL

### Installation Guide

1.  Clone the repository: `git clone https://github.com/username/crate-digger.git` `cd crate-digger`
2.  Install dependencies: `composer install`
3.  Configure the `.env` file with database credentials and Discogs API keys: `DISCOGS_KEY=your_key` `DISCOGS_SECRET=your_secret` `DISCOGS_REDIRECT_URI=your_redirect_uri`
4.  Generate the application key: `php artisan key:generate`
5.  Run the database migrations: `php artisan migrate`


# Login Token Moodle Plugin

A Moodle plugin for token-based authentication using Moodle web service tokens (wstoken), allowing users to log in via API calls.

## Features

-   API-based authentication using Moodle web service tokens
-   Support for both custom Login Token service and Moodle Mobile App service tokens
-   Simple REST endpoint for token validation and session creation
-   Secure token validation with expiration checking
-   JSON response format for easy integration
-   Automatic session management

## Requirements

-   Moodle version 3.9 or higher
-   Web service tokens configured in Moodle
-   Valid wstoken for authentication

## Installation

1.  **Download the Plugin:** Download or clone the plugin from the repository
2.  **Move to Moodle Local Plugins:** Move the `logintoken` folder to the `moodle/local/` directory
3.  **Install via Moodle Interface:**
    -   Navigate to `Site administration > Notifications` in your Moodle site
    -   Moodle will detect the new plugin. Follow the on-screen instructions to complete the installation
4.  **Configure the Plugin:**
    -   After installation, go to `Site administration > Plugins > Local plugins > Login Token`
    -   Configure the plugin settings as needed

## Usage

### Web Service API

This plugin provides Moodle web service functions for token-based authentication:

#### Available Functions

1. **`local_logintoken_login_with_token`** - Login user with web service token
2. **`local_logintoken_validate_token`** - Validate web service token

### Supported Services

The plugin works with tokens from:
- **Login Token Service** (custom service)
- **Moodle Mobile App Service** (built-in service)

### API Endpoints

#### 1. Login with Token
```
GET /local/logintoken/auth/logintoken.php?wstoken=YOUR_TOKEN
```

#### 2. Validate Token
```
GET /local/logintoken/auth/logintoken.php?wstoken=YOUR_TOKEN&wsfunction=local_logintoken_validate_token
```

### Example Usage

#### Login with Token (Custom Service)
```bash
curl "https://your-moodle-site.com/local/logintoken/auth/logintoken.php?wstoken=abc123def456"
```

#### Login with Token (Mobile App Service)
```bash
curl "https://your-moodle-site.com/local/logintoken/auth/logintoken.php?wstoken=mobile_token_here"
```

#### Validate Token
```bash
curl "https://your-moodle-site.com/local/logintoken/auth/logintoken.php?wstoken=abc123def456&wsfunction=local_logintoken_validate_token"
```

### Response Format

#### Login Success Response:
```json
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 123,
        "username": "john.doe",
        "firstname": "John",
        "lastname": "Doe",
        "email": "john.doe@example.com",
        "fullname": "John Doe"
    },
    "redirect_url": "https://your-moodle-site.com/",
    "session_id": "abc123def456"
}
```

#### Token Validation Response:
```json
{
    "valid": true,
    "message": "Token is valid",
    "user": {
        "id": 123,
        "username": "john.doe",
        "firstname": "John",
        "lastname": "Doe",
        "email": "john.doe@example.com",
        "fullname": "John Doe"
    },
    "token_info": {
        "created": 1701234567,
        "last_access": 1701234567,
        "valid_until": 1701320967
    }
}
```

#### Error Response:
```json
{
    "success": false,
    "error": "Invalid token",
    "message": "The provided wstoken is invalid or expired"
}
```

## Development

### Code Structure

-   `auth/logintoken.php`: Main API endpoint for token-based authentication
-   `settings.php`: Defines the plugin settings for configuration
-   `lib.php`: Contains the core logic for login page handling
-   `lang/en/local_logintoken.php`: Contains language strings for the plugin

### Security Features

-   Token validation against Moodle's external_tokens table
-   Token expiration checking
-   User status validation (not deleted or suspended)
-   HTTPS requirement option
-   Session management

## Contributing

1.  Fork the repository
2.  Create a new branch (`git checkout -b feature/your-feature`)
3.  Commit your changes (`git commit -m 'Add some feature'`)
4.  Push to the branch (`git push origin feature/your-feature`)
5.  Open a pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

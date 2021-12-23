# Hex-Chatter
Basic chatting app that works with websockets.  
Uses Redis, Socket.io, MariaDb.

## Installation
Coming soon...

## Methods:

### Auth
| Method | URI | Description |
|----------|-------------------------|-------------------------------|
| **GET**  | /                       | Basic page with some info.    |
| **GET**  | /login                  | Auth page.                    |
| **GET**  | /register               | Register page.                |

### Endpoints
| Method | URI | Description |
|----------|-------------------------|-------------------------------|
| **GET**  | /messenger              | List of all available users.  |
| **GET**  | /messenger/{recipient}  | Messenger page.               |
| **POST** | /messenger/send         | Used to send AJAX request.    |

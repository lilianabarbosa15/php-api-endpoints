# php-api-endpoints


## Authentication

### POST `/api/v1/register`
- **Body:** `{name, email, password}` for a new user. The email is checked to ensure it doesn’t already exist in the database, and all other fields are validated.
- **Returns:**
  - `HTTP/201` and the newly created user if everything is correct.
  - `HTTP/422` (Unprocessable Entity) if there’s an issue with the input data.

### GET `/api/v1/profile`
- **Header:** This endpoint is protected by Sanctum Middleware, so it requires an authorization token obtained from the login endpoint.
- **Returns:**
  - `HTTP/200` and the user's profile if everything is correct.
  - `HTTP/401` (Unauthorized) if there’s an issue with the token.

### POST `/api/v1/login`
- **Body:** `{email, password}` for an existing user. It verifies both fields.
- **Returns:**
  - `HTTP/200` and the authenticated user if everything is correct.
  - `HTTP/401` (Unauthorized) if there’s an issue with the input data.

### POST `/api/v1/logout`
- **Body:** `{email, password}` for an existing user. It verifies both fields.
- **Returns:**
  - `HTTP/200` and a message `"Logged out successfully"` if everything is correct.
  - `HTTP/401` (Unauthorized) if there’s an issue with the input data.
  - `HTTP/400` (Bad Request) if the data is correct, but the user isn’t logged in.

---

## Products - ProductVariants

### GET `/api/v1/products`
- **Description:** This endpoint can be accessed by anyone, no authentication required to view the products. You can also pass the page number and the number of items you want to see in the response.
  - Example: `http://api-endpoints.test/api/v1/products?per_page=4&page=4`
  - Example: `http://api-endpoints.test/api/v1/products?page=4`
  - Example: `http://api-endpoints.test/api/v1/products?per_page=4`
- **Returns:**
  - `HTTP/200` if the products are found.
  - `HTTP/404` if no products are found.

### POST `/api/v1/products` (protected)
- **Body:** Create new products in the database with the following structure:
  ```json
  {
    "name": "SQ1 Hoodie",
    "description": "A hoodie perfect for winter with excellent quality at a good price.",
    "price": 50.99,
    "other_attributes": {
        "material": "cotton",
        "pattern": "solid",
        "brand": "PRADA",
        "care_instructions": "Wash and dry in the shade to extend its durability.",
        "collection": "autumn",
        "gender": "unisex"
    }
  }

- **Description:** Create a new product in the database.
- **Returns:**
  - `HTTP/201` if the new product is successfully created.
  - `HTTP/422` if there is an issue with the input data.

### GET `/api/v1/products/search`
- **Description:** This endpoint can be accessed by anyone, no authentication required. It allows filtering products by attributes like name, color, size, brand, collection, price, and gender.
- **Examples:**
  - Filter by color:  
    `http://api-endpoints.test/api/v1/products/search?color=%2308682a`
  - Filter by name:  
    `http://api-endpoints.test/api/v1/products/search?name=Molestias_saepe_consequatur`
  - Filter by price range:  
    `http://api-endpoints.test/api/v1/products/search?max_price=192.38&min_price=100`
  - Filter by size:  
    `http://api-endpoints.test/api/v1/products/search?size=L`
  - Filter by brand:  
    `http://api-endpoints.test/api/v1/products/search?attributes=brand&value=GUCCI`
- **Returns:**
  - `HTTP/200` if products matching the criteria are found.
  - `HTTP/404` if no matching products are found.

### GET `/api/v1/products/{id}`
- **Description:** This endpoint can be accessed by anyone, no authentication required. It allows searching for a specific product by its ID.
- **Returns:**
  - `HTTP/200` if the product is found.
  - `HTTP/404` if the product is not found.

### PUT `/api/v1/products/{id}` (protected)
- **Body:** Update an existing product in the database with the new data.
- **Returns:**
  - `HTTP/200` if the product is successfully updated.
  - `HTTP/422` if there is an issue with the input data.
  - `HTTP/404` if the product with the given ID is not found.

### DELETE `/api/v1/products/{id}` (protected)
- **No body required.**
- **Returns:**
  - `HTTP/404` if the product is not found.
  - `HTTP/204` if the product is successfully deleted.

## Notes:
- The "protected" endpoints are currently not secured by Sanctum middleware and are publicly accessible.
- In future versions, these endpoints will be secured with Sanctum, allowing only the admin to make changes.

---

## Orders

### GET /api/v1/orders
- **Description:** This endpoint checks the logged-in user and returns all orders associated with that user.
- **Returns:**
  - `HTTP/200` with an array of orders associated with the logged-in user, even if the array is empty.

### GET /api/v1/orders/{id}
- **Description:** Based on the logged-in user, this endpoint searches for a specific order.
- **Returns:**
  - `HTTP/404` if the specified order is not found in the database.
  - `HTTP/403` if the order belongs to a different user.
  - `HTTP/200` if the order is found and belongs to the logged-in user.

### POST /api/v1/orders/create
- **Description:** Creates a new order. The order data should be included in the request body, including order items.
- **Example Body:**
  ```json
  {
      "order_status": "pending",
      "payment_method": "Visa",
      "shipping_address": "Torre Navarra apt. x ",
      "order_items": [
          {
              "product_variant_id": 2,
              "quantity": 10
          },
          {
              "product_variant_id": 20,
              "quantity": 5
          },
          {
              "product_variant_id": 3000,
              "quantity": 7
          }
      ]
  }
- **Returns:**
  - `HTTP/422` if the cart is empty, if the product variant does not exist (product_variant_id), or if any input data in the body is incorrect.
  - `HTTP/201` if the order is successfully created.


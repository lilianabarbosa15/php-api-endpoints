# php-api-endpoints

---

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
              "product_variant_id": 30,
              "quantity": 7
          }
      ]
  }
- **Returns:**
  - `HTTP/422` if the cart is empty, if the product variant does not exist (product_variant_id), or if any input data in the body is incorrect.
  - `HTTP/201` if the order is successfully created.

## Notes:
- All headers require authorization via a token provided at the login endpoint, as all endpoints are protected by the Sanctum Middleware.

---

## Shopping Cart

### GET /api/v1/cart/
- **Description:** Verifies the logged-in user and returns all shopping carts along with their cart items based on the user's session.
- **Returns:**
  - `HTTP/200` even if no shopping carts are found.
  - `HTTP/401` if the user is not authenticated.

### POST /api/v1/cart/add
- **Description:** Adds one or more products of type `ProductVariant` (via the relationship with `CartItems`) to a `ShoppingCart`. The process verifies the stock availability for each variant and updates it accordingly.
- **Example Body:**
  ```json
  {
      "cart_items": [
          {
              "product_variant_id": 20,
              "quantity": 1
          },
          {
              "product_variant_id": 3,
              "quantity": 6
          },
          {
              "product_variant_id": 1,
              "quantity": 5
          }
      ]
  }
- **Returns:**
  - `HTTP/201` if the shopping cart is successfully created.
  - `HTTP/422` if the quantity exceeds the available stock.
  - `HTTP/404` if any product variant does not exist.
  - `HTTP/401` if the user is not authenticated.

### PUT /api/v1/cart/update/{id}
- **Description:** The id in the URL corresponds to a cart_item_id. You must also provide the desired quantity in order to update the quantity of a product in the shopping cart. This cart corresponds to the last shopping cart associated with the authenticated user. After identifying the cart, its associated items are checked, and the specific item with the provided id is updated. The associated ProductVariant is also updated to reflect the stock changes.
- **Example Body:**
  ```json
  {
    "quantity": 20
  }
- **Returns:**
  - `HTTP/201` if the quantity of the item is successfully updated.
  - `HTTP/401` if the user is not authenticated.
  - `HTTP/404` if the cart_item_id is not found or if the shopping cart has no items.
  - `HTTP/422` if the updated quantity exceeds the available stock.
  - `HTTP/400` if the provided ID is not numeric.

### DELETE /api/v1/cart/remove/{id}
- **Description:** The `id` in the URL corresponds to a `cart_item_id`. The difference compared to the `PUT /api/v1/cart/update/{id}` method is that no body is required for this request. This endpoint removes the specified item from the shopping cart.
- **Returns:**
  - `HTTP/204` if the item is successfully removed.
  - `HTTP/401` if the user is not authenticated.
  - `HTTP/404` if the cart_item_id is not found or if the shopping cart has no items.
  - `HTTP/400` if the provided ID is not numeric.

## Notes:
- All headers require authorization via a token provided at the login endpoint, as all endpoints are protected by the Sanctum Middleware.

-----
**[Instructions to Run the Project Locally](https://github.com/lilianabarbosa15/php-api-endpoints/tree/main/api-endpoints/)**



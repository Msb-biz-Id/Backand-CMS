# API Documentation - Advanced CMS

## Overview

The CMS provides a RESTful API for accessing content programmatically. All API endpoints return JSON responses and require authentication for write operations.

## Base URL

```
https://your-domain.com/api
```

## Authentication

### API Key Authentication

Include your API key in the request header:

```
Authorization: Bearer YOUR_API_KEY
```

### Getting an API Key

1. Login to admin panel
2. Navigate to Settings > API
3. Generate new API key
4. Copy and securely store your key

## Rate Limiting

- **Default**: 100 requests per minute
- **Headers**: Rate limit info included in response headers

```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1609459200
```

## Response Format

### Success Response

```json
{
  "success": true,
  "data": {
    // Response data
  },
  "meta": {
    "page": 1,
    "per_page": 15,
    "total": 100
  }
}
```

### Error Response

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Error description",
    "details": {}
  }
}
```

## Endpoints

### Posts

#### Get All Posts

```
GET /api/posts
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| page | integer | Page number (default: 1) |
| per_page | integer | Items per page (default: 15, max: 100) |
| status | string | Filter by status: published, draft, scheduled |
| category | integer | Filter by category ID |
| tag | integer | Filter by tag ID |
| search | string | Search in title and content |
| order_by | string | Sort field (default: published_at) |
| order | string | Sort direction: asc, desc (default: desc) |

**Example Request:**

```bash
curl -X GET "https://your-domain.com/api/posts?page=1&per_page=10" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

**Example Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Sample Post",
      "slug": "sample-post",
      "excerpt": "This is a sample post...",
      "content": "Full post content...",
      "featured_image": "/uploads/posts/image.jpg",
      "status": "published",
      "published_at": "2024-01-15 10:00:00",
      "views": 150,
      "author": {
        "id": 1,
        "username": "admin",
        "first_name": "John",
        "last_name": "Doe"
      },
      "categories": [
        {
          "id": 1,
          "name": "Technology",
          "slug": "technology"
        }
      ],
      "tags": [
        {
          "id": 1,
          "name": "PHP",
          "slug": "php"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 100,
    "last_page": 10
  }
}
```

#### Get Single Post

```
GET /api/posts/{id}
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | Post ID |

**Alternative: Get by Slug**

```
GET /api/posts/slug/{slug}
```

**Example:**

```bash
curl -X GET "https://your-domain.com/api/posts/1" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

#### Create Post

```
POST /api/posts
```

**Required Authentication**: Yes

**Body Parameters:**

```json
{
  "title": "New Post Title",
  "slug": "new-post-title",
  "content": "Post content here...",
  "excerpt": "Short description",
  "featured_image": "/uploads/posts/image.jpg",
  "status": "published",
  "published_at": "2024-01-15 10:00:00",
  "categories": [1, 2],
  "tags": [1, 2, 3],
  "seo": {
    "meta_title": "SEO Title",
    "meta_description": "SEO description",
    "focus_keyword": "keyword"
  }
}
```

#### Update Post

```
PUT /api/posts/{id}
```

**Required Authentication**: Yes

#### Delete Post

```
DELETE /api/posts/{id}
```

**Required Authentication**: Yes

### Products

#### Get All Products

```
GET /api/products
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| page | integer | Page number |
| per_page | integer | Items per page |
| category | integer | Filter by category |
| min_price | decimal | Minimum price |
| max_price | decimal | Maximum price |
| in_stock | boolean | Filter by stock status |
| featured | boolean | Filter featured products |

**Example Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Product Name",
      "slug": "product-name",
      "sku": "PROD-001",
      "description": "Product description",
      "price": 99.99,
      "sale_price": 79.99,
      "currency": "IDR",
      "stock": 100,
      "in_stock": true,
      "featured_image": "/uploads/products/image.jpg",
      "gallery": [
        "/uploads/products/gallery1.jpg",
        "/uploads/products/gallery2.jpg"
      ],
      "categories": [...],
      "tags": [...]
    }
  ]
}
```

#### Get Single Product

```
GET /api/products/{id}
```

### Pages

#### Get All Pages

```
GET /api/pages
```

#### Get Single Page

```
GET /api/pages/{id}
GET /api/pages/slug/{slug}
```

### Jobs

#### Get All Jobs

```
GET /api/jobs
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| employment_type | string | full_time, part_time, contract, etc |
| location | string | Filter by location |
| active | boolean | Only active job listings |

#### Get Single Job

```
GET /api/jobs/{id}
```

#### Apply for Job

```
POST /api/jobs/{id}/apply
```

**Body:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "resume": "file_id_or_url",
  "cover_letter": "Cover letter text..."
}
```

### Categories

#### Get All Categories

```
GET /api/categories
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| type | string | Filter by type: post, product, job, page |

#### Get Single Category

```
GET /api/categories/{id}
```

### Tags

#### Get All Tags

```
GET /api/tags
```

#### Get Single Tag

```
GET /api/tags/{id}
```

### Media

#### Get All Media

```
GET /api/media
```

#### Upload Media

```
POST /api/media/upload
```

**Content-Type**: multipart/form-data

**Body:**

```
file: [binary file data]
title: "Image title"
alt_text: "Alternative text"
```

### Analytics

#### Get Statistics

```
GET /api/analytics/stats
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| start_date | date | Start date (Y-m-d) |
| end_date | date | End date (Y-m-d) |
| type | string | Filter by content type |

**Response:**

```json
{
  "success": true,
  "data": {
    "page_views": 10000,
    "unique_visitors": 5000,
    "avg_time_on_page": 125,
    "bounce_rate": 45.5,
    "popular_posts": [...],
    "referrers": [...]
  }
}
```

### Search

#### Global Search

```
GET /api/search
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| q | string | Search query (required) |
| type | string | Filter by type: post, page, product, job |
| limit | integer | Results limit |

## Webhooks

### Registering Webhooks

Configure webhooks in Admin Panel > Settings > Webhooks

### Webhook Events

- `post.created`
- `post.updated`
- `post.deleted`
- `product.created`
- `product.updated`
- `product.stock_low`
- `user.registered`
- `comment.created`

### Webhook Payload

```json
{
  "event": "post.created",
  "timestamp": "2024-01-15T10:00:00Z",
  "data": {
    // Event-specific data
  }
}
```

## Error Codes

| Code | HTTP Status | Description |
|------|------------|-------------|
| UNAUTHORIZED | 401 | Invalid or missing API key |
| FORBIDDEN | 403 | Insufficient permissions |
| NOT_FOUND | 404 | Resource not found |
| VALIDATION_ERROR | 422 | Invalid input data |
| RATE_LIMIT_EXCEEDED | 429 | Too many requests |
| SERVER_ERROR | 500 | Internal server error |

## Best Practices

1. **Use HTTPS**: Always use HTTPS for API requests
2. **Cache Responses**: Cache GET requests when appropriate
3. **Handle Rate Limits**: Implement exponential backoff
4. **Validate Input**: Always validate data before sending
5. **Handle Errors**: Implement proper error handling
6. **Keep Keys Secure**: Never expose API keys in client-side code

## SDK Examples

### PHP

```php
<?php

$apiKey = 'YOUR_API_KEY';
$baseUrl = 'https://your-domain.com/api';

$ch = curl_init($baseUrl . '/posts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$data = json_decode($response, true);

curl_close($ch);
```

### JavaScript

```javascript
const apiKey = 'YOUR_API_KEY';
const baseUrl = 'https://your-domain.com/api';

fetch(`${baseUrl}/posts`, {
  headers: {
    'Authorization': `Bearer ${apiKey}`,
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

### Python

```python
import requests

api_key = 'YOUR_API_KEY'
base_url = 'https://your-domain.com/api'

headers = {
    'Authorization': f'Bearer {api_key}',
    'Content-Type': 'application/json'
}

response = requests.get(f'{base_url}/posts', headers=headers)
data = response.json()

print(data)
```

## Support

For API support and questions:
- Email: api-support@your-domain.com
- Documentation: https://your-domain.com/docs/api
- Community Forum: https://forum.your-domain.com

## Changelog

### Version 1.0.0 (2024-01-15)
- Initial API release
- RESTful endpoints for all content types
- Authentication and rate limiting
- Comprehensive error handling

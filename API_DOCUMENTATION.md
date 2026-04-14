# API Documentation - EduDev Lessons

## Overview
The API provides RESTful endpoints for managing lessons. All endpoints require authentication via the standard Laravel session auth.

## Base URL
```
/api/lessons
```

## Authentication
All API requests require the user to be authenticated. Authentication is session-based.

### Example with Axios (JavaScript/Vue):
```javascript
// Axios automatically includes session cookies
import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    }
});

export default api;
```

---

## Endpoints

### 1. List Lessons (Paginated)
**GET** `/api/lessons`

#### Query Parameters:
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Results per page (default: 12, max: 100)
- `type` (string): Filter by type - `video`, `text`, or `document`
- `difficulty` (string): Filter by difficulty - `beginner`, `intermediate`, or `advanced`
- `published` (boolean): Filter by publish status - `true` or `false`
- `q` (string): Search by title or slug

#### Response (200 OK):
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "title": "Introduction to PHP",
            "slug": "introduction-to-php",
            "type": "video",
            "video_url": "https://youtube.com/...",
            "difficulty": "beginner",
            "is_published": true,
            "is_free": true,
            "duration_minutes": 45,
            "thumbnail": "thumbnails/...",
            "created_at": "2026-03-06T10:00:00Z",
            "updated_at": "2026-03-06T10:00:00Z"
        }
    ],
    "meta": {
        "total": 25,
        "per_page": 12,
        "current_page": 1,
        "last_page": 3
    }
}
```

#### Example Requests:
```bash
# Get first page
curl -X GET "http://localhost:8000/api/lessons" \
    -H "Accept: application/json" \
    -b "XSRF-TOKEN=..."

# Get beginner video lessons
curl -X GET "http://localhost:8000/api/lessons?type=video&difficulty=beginner" \
    -H "Accept: application/json"

# Search lessons
curl -X GET "http://localhost:8000/api/lessons?q=php&per_page=20" \
    -H "Accept: application/json"
```

---

### 2. Get Single Lesson
**GET** `/api/lessons/{id-or-slug}`

#### Parameters:
- `{id-or-slug}`: Lesson ID (integer) or slug (string)

#### Response (200 OK):
```json
{
    "success": true,
    "data": {
        "id": 1,
        "user_id": 1,
        "title": "Introduction to PHP",
        "slug": "introduction-to-php",
        "type": "video",
        "video_url": "https://youtube.com/...",
        "content": "...",
        "segments": [
            {
                "id": 1,
                "custom_name": "Getting Started",
                "type": "content",
                "blocks": [
                    {
                        "id": 1,
                        "type": "text",
                        "content": "Welcome to the lesson..."
                    },
                    {
                        "id": 2,
                        "type": "image",
                        "path": "lesson-images/...",
                        "caption": "Diagram example"
                    }
                ]
            }
        ],
        "difficulty": "beginner",
        "is_published": true,
        "is_free": true,
        "duration_minutes": 45,
        "created_at": "2026-03-06T10:00:00Z",
        "updated_at": "2026-03-06T10:00:00Z"
    }
}
```

#### Errors:
- `404 Not Found`: Lesson doesn't exist
- `403 Forbidden`: Lesson is not published and user doesn't own it

---

### 3. Create Lesson
**POST** `/api/lessons`

#### Request Body (multipart/form-data for file uploads):
```json
{
    "title": "PHP Basics",
    "type": "video",
    "video_url": "https://youtube.com/watch?v=...",
    "duration_hours": 1,
    "duration_minutes": 30,
    "difficulty": "beginner",
    "is_published": false,
    "is_free": true,
    "thumbnail": <file>,
    "segments": [
        {
            "custom_name": "Introduction",
            "blocks": [
                {
                    "type": "text",
                    "content": "Welcome to PHP!"
                },
                {
                    "type": "image",
                    "image": <file>,
                    "content": "Example diagram"
                }
            ]
        },
        {
            "custom_name": "Quiz",
            "blocks": [],
            "exam_settings": {
                "time_limit": 600,
                "passing_score": 70
            },
            "questions": [
                {
                    "type": "multiple_choice",
                    "question": "What is PHP?",
                    "answers": [
                        "Server-side language",
                        "Client-side language",
                        "Database"
                    ],
                    "correct_answer": 0
                }
            ]
        }
    ]
}
```

#### Response (201 Created):
```json
{
    "success": true,
    "message": "Lesson created successfully",
    "data": {
        "id": 2,
        "user_id": 1,
        "title": "PHP Basics",
        "slug": "php-basics",
        ...
    }
}
```

#### Example with Axios:
```javascript
const createLesson = async (formData) => {
    try {
        const response = await api.post('/lessons', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        return response.data;
    } catch (error) {
        console.error('Error creating lesson:', error.response.data);
        throw error;
    }
};
```

---

### 4. Update Lesson
**PUT** `/api/lessons/{id}`

#### Parameters:
- `{id}`: Lesson ID (required)

#### Request Body:
Same as Create Lesson - all fields are optional except `title` and `type`

#### Response (200 OK):
```json
{
    "success": true,
    "message": "Lesson updated successfully",
    "data": { ... }
}
```

---

### 5. Delete Lesson
**DELETE** `/api/lessons/{id}`

#### Parameters:
- `{id}`: Lesson ID (required)

#### Response (200 OK):
```json
{
    "success": true,
    "message": "Lesson deleted successfully"
}
```

#### Behavior:
- Deletes the lesson record
- Deletes all associated files (thumbnail, document, block images/files)
- Only lesson owner can delete

---

## Error Responses

### 422 Unprocessable Entity (Validation Error)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "title": ["The title field is required."],
        "video_url": ["The video url must be a valid URL."]
    }
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Lesson not found"
}
```

---

## Content Block Types

### Text Block
```json
{
    "type": "text",
    "content": "Your content here"
}
```

### Subheading Block
```json
{
    "type": "subheading",
    "content": "Subheading text"
}
```

### Video Block
```json
{
    "type": "video",
    "content": "https://youtube.com/embed/..."
}
```

### Image Block
```json
{
    "type": "image",
    "image": <file>,
    "content": "Image caption"
}
```

### File Block
```json
{
    "type": "file",
    "file": <file>
}
```

### Callout Block
```json
{
    "type": "callout",
    "content": "Important information",
    "callout_type": "info" // or "warning", "success", "danger"
}
```

### Code Block
```json
{
    "type": "code",
    "content": "<?php echo 'Hello'; ?>",
    "language": "php" // or "javascript", "python", etc.
}
```

### Divider Block
```json
{
    "type": "divider"
}
```

### Quiz Block
```json
{
    "type": "quiz",
    "question": "What is 2+2?",
    "answers": ["3", "4", "5"],
    "correct_answer": 1
}
```

---

## Example: Complete Frontend Integration

### Using Vue 3 with Axios

```javascript
// api/lessons.js
import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    }
});

export const lessons = {
    // GET /api/lessons
    list: (params = {}) => 
        api.get('/lessons', { params }),

    // GET /api/lessons/:id
    get: (id) => 
        api.get(`/lessons/${id}`),

    // POST /api/lessons
    create: (data) => 
        api.post('/lessons', data, {
            headers: { 'Content-Type': 'multipart/form-data' }
        }),

    // PUT /api/lessons/:id
    update: (id, data) => 
        api.put(`/lessons/${id}`, data, {
            headers: { 'Content-Type': 'multipart/form-data' }
        }),

    // DELETE /api/lessons/:id
    delete: (id) => 
        api.delete(`/lessons/${id}`),
};

export default api;
```

### Using in Vue Component

```vue
<script setup>
import { ref, onMounted } from 'vue';
import { lessons } from '@/api/lessons';

const lessonsList = ref([]);
const loading = ref(false);
const error = ref(null);

onMounted(async () => {
    await fetchLessons();
});

const fetchLessons = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await lessons.list({ 
            per_page: 20,
            type: 'video'
        });
        lessonsList.value = response.data.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch lessons';
    } finally {
        loading.value = false;
    }
};

const deleteLessonItem = async (id) => {
    if (!confirm('Are you sure?')) return;
    
    try {
        await lessons.delete(id);
        lessonsList.value = lessonsList.value.filter(l => l.id !== id);
    } catch (err) {
        error.value = 'Failed to delete lesson';
    }
};
</script>

<template>
    <div class="lessons">
        <h1>My Lessons</h1>
        
        <div v-if="error" class="error">{{ error }}</div>
        
        <div v-if="loading" class="loading">Loading...</div>
        
        <div v-else class="lesson-grid">
            <div v-for="lesson in lessonsList" :key="lesson.id" class="lesson-card">
                <h3>{{ lesson.title }}</h3>
                <p>{{ lesson.difficulty }}</p>
                <button @click="deleteLessonItem(lesson.id)">Delete</button>
            </div>
        </div>
    </div>
</template>
```

---

## Rate Limiting & Best Practices

1. **Pagination**: Always use pagination for listing endpoints to avoid performance issues
2. **Caching**: Cache GET requests client-side when appropriate
3. **Error Handling**: Always handle validation errors (422) with user-friendly messages
4. **File Uploads**: Keep files under size limits (5MB for images, 20MB for documents)
5. **CSRF Protection**: Axios is configured to automatically include CSRF tokens

---

## Migration from Web Routes

You can now replace web route form submissions with API calls:

**Before:**
```blade
<form action="/lessons" method="POST">
    @csrf
    <input name="title">
    <button type="submit">Create</button>
</form>
```

**After:**
```javascript
// JavaScript
async function createLesson(formData) {
    const response = await api.post('/lessons', formData);
    window.location.href = `/lessons/${response.data.data.id}`;
}
```

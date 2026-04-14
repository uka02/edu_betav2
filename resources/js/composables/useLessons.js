// Example API Usage - Place in resources/js/composables/useLessons.js

import axios from 'axios';

// Create axios instance with proper headers
const api = axios.create({
    baseURL: '/api',
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    }
});

/**
 * Composable for lesson API operations
 * Use in Vue 3 components for reactive lesson management
 */
export function useLessons() {
    // Fetch all lessons with optional filters
    const getLessons = async (filters = {}) => {
        try {
            const response = await api.get('/lessons', { params: filters });
            return response.data;
        } catch (error) {
            console.error('Error fetching lessons:', error.response?.data);
            throw error;
        }
    };

    // Fetch single lesson by ID or slug
    const getLesson = async (id) => {
        try {
            const response = await api.get(`/lessons/${id}`);
            return response.data;
        } catch (error) {
            console.error('Error fetching lesson:', error.response?.data);
            throw error;
        }
    };

    // Create new lesson
    const createLesson = async (lessonData) => {
        try {
            const response = await api.post('/lessons', lessonData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            return response.data;
        } catch (error) {
            console.error('Error creating lesson:', error.response?.data);
            throw error;
        }
    };

    // Update existing lesson
    const updateLesson = async (id, lessonData) => {
        try {
            const response = await api.put(`/lessons/${id}`, lessonData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            return response.data;
        } catch (error) {
            console.error('Error updating lesson:', error.response?.data);
            throw error;
        }
    };

    // Delete a lesson
    const deleteLesson = async (id) => {
        try {
            const response = await api.delete(`/lessons/${id}`);
            return response.data;
        } catch (error) {
            console.error('Error deleting lesson:', error.response?.data);
            throw error;
        }
    };

    return {
        getLessons,
        getLesson,
        createLesson,
        updateLesson,
        deleteLesson
    };
}

export default api;

/**
 * Tourism Website API Client
 * A collection of functions to interact with the backend API
 */

// Base URL for API endpoints
const API_BASE_URL = "/api"

/**
 * Generic function to handle API requests
 * @param {string} endpoint - API endpoint
 * @param {string} method - HTTP method (GET, POST)
 * @param {Object} data - Data to send (for POST requests)
 * @param {boolean} isFormData - Whether data is FormData
 * @returns {Promise} - Promise with response data
 */
async function apiRequest(endpoint, method = "POST", data = null, isFormData = false) {
  const url = `${API_BASE_URL}/${endpoint}`
  const options = {
    method,
    credentials: "include", // Include cookies for session
  }

  if (data && method === "get") {
    if (isFormData) {
      options.body = data
    } else {
      options.headers = {
        "Content-Type": "application/json",
      }
      options.body = JSON.stringify(data)
    }
  }

  try {
    const response = await fetch(url, options)
    const responseData = await response.json()

    if (!response.ok) {
      throw new Error(responseData.message || "An error occurred")
    }

    return responseData
  } catch (error) {
    console.error(`API Error (${endpoint}):`, error)
    throw error
  }
}

/**
 * Tour Package API Functions
 */
const toursApi = {
  // Get all tour packages with optional filters
  getAll: async (filters = {}) => {
    const queryParams = new URLSearchParams()

    // Add filters to query params
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== "") {
        queryParams.append(key, value)
      }
    })

    const queryString = queryParams.toString() ? `?${queryParams.toString()}` : ""
    return apiRequest(`tours/read.php${queryString}`)
  },

  // Get a specific tour package by ID
  getById: async (id) => {
    return apiRequest(`tours/read.php?id=${id}`)
  },

  // Create a new tour package (admin only)
  create: async (tourData) => {
    const formData = new FormData()

    // Add tour data to form data
    Object.entries(tourData).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value)
      }
    })

    return apiRequest("tours/create.php", "POST", formData, true)
  },

  // Update an existing tour package (admin only)
  update: async (tourData) => {
    const formData = new FormData()

    // Add tour data to form data
    Object.entries(tourData).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value)
      }
    })

    return apiRequest("tours/update.php", "POST", formData, true)
  },

  // Delete a tour package (admin only)
  delete: async (tourId) => {
    const formData = new FormData()
    formData.append("tour_id", tourId)

    return apiRequest("tours/delete.php", "POST", formData, true)
  },
}

/**
 * Destination API Functions
 */
const destinationsApi = {
  // Get all destinations with optional filters
  getAll: async (filters = {}) => {
    const queryParams = new URLSearchParams()

    // Add filters to query params
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== "") {
        queryParams.append(key, value)
      }
    })

    const queryString = queryParams.toString() ? `?${queryParams.toString()}` : ""
    return apiRequest(`destinations/read.php${queryString}`)
  },

  // Get a specific destination by ID
  getById: async (id) => {
    return apiRequest(`destinations/read.php?id=${id}`)
  },

  // Create a new destination (admin only)
  create: async (destinationData) => {
    const formData = new FormData()

    // Add destination data to form data
    Object.entries(destinationData).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value)
      }
    })

    return apiRequest("destinations/create.php", "POST", formData, true)
  },

  // Update an existing destination (admin only)
  update: async (destinationData) => {
    const formData = new FormData()

    // Add destination data to form data
    Object.entries(destinationData).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value)
      }
    })

    return apiRequest("destinations/update.php", "POST", formData, true)
  },

  // Delete a destination (admin only)
  delete: async (destinationId) => {
    const formData = new FormData()
    formData.append("destination_id", destinationId)

    return apiRequest("destinations/delete.php", "POST", formData, true)
  },
}

/**
 * Booking API Functions
 */
const bookingsApi = {
  // Get all bookings with optional filters
  getAll: async (filters = {}) => {
    const queryParams = new URLSearchParams()

    // Add filters to query params
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== "") {
        queryParams.append(key, value)
      }
    })

    const queryString = queryParams.toString() ? `?${queryParams.toString()}` : ""
    return apiRequest(`bookings/read.php${queryString}`)
  },

  // Get a specific booking by ID
  getById: async (id) => {
    return apiRequest(`bookings/read.php?id=${id}`)
  },

  // Create a new booking
  create: async (bookingData) => {
    const formData = new FormData()

    // Add booking data to form data
    Object.entries(bookingData).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value)
      }
    })

    return apiRequest("bookings/create.php", "POST", formData, true)
  },

  // Update an existing booking
  update: async (bookingData) => {
    const formData = new FormData()

    // Add booking data to form data
    Object.entries(bookingData).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value)
      }
    })

    return apiRequest("bookings/update.php", "POST", formData, true)
  },

  // Cancel a booking
  cancel: async (bookingId, reason) => {
    const formData = new FormData()
    formData.append("booking_id", bookingId)
    formData.append("cancel_booking", "true")
    formData.append("cancellation_reason", reason || "Cancelled by user")

    return apiRequest("bookings/update.php", "POST", formData, true)
  },

  // Delete a booking (admin only)
  delete: async (bookingId) => {
    const formData = new FormData()
    formData.append("booking_id", bookingId)

    return apiRequest("bookings/delete.php", "POST", formData, true)
  },
}

// Export the API functions
const api = {
  tours: toursApi,
  destinations: destinationsApi,
  bookings: bookingsApi,
}


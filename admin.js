import { Chart } from "@/components/ui/chart"
/**
 * Admin Panel JavaScript
 * Handles CRUD operations for the admin panel
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize components based on current page
  const currentPage = window.location.pathname.split("/").pop().split(".")[0]

  // Initialize page-specific functionality
  switch (currentPage) {
    case "tours":
      initTourManagement()
      break
    case "destinations":
      initDestinationManagement()
      break
    case "bookings":
      initBookingManagement()
      break
    case "users":
      initUserManagement()
      break
    case "dashboard":
    default:
      initDashboard()
      break
  }

  // Show success/error messages
  function showMessage(message, isError = false) {
    const messageContainer = document.getElementById("message-container")
    if (!messageContainer) return

    const messageElement = document.createElement("div")
    messageElement.className = `alert ${isError ? "alert-danger" : "alert-success"}`
    messageElement.textContent = message

    messageContainer.innerHTML = ""
    messageContainer.appendChild(messageElement)

    // Auto-hide message after 5 seconds
    setTimeout(() => {
      messageElement.remove()
    }, 5000)
  }

  // Tour Package Management
  function initTourManagement() {
    const tourForm = document.getElementById("tour-form")
    const toursList = document.getElementById("tours-list")
    const searchForm = document.getElementById("search-form")

    // Load tour packages
    loadTours()

    // Handle form submission for creating/updating tour packages
    if (tourForm) {
      tourForm.addEventListener("submit", async (e) => {
        e.preventDefault()

        const formData = new FormData(tourForm)
        const tourId = formData.get("tour_id")

        try {
          let response
          if (tourId) {
            // Update existing tour
            response = await api.tours.update(formData)
            showMessage("Tour package updated successfully")
          } else {
            // Create new tour
            response = await api.tours.create(formData)
            showMessage("Tour package created successfully")
          }

          // Reset form and reload tours
          tourForm.reset()
          document.getElementById("tour-form-title").textContent = "Add New Tour Package"
          document.getElementById("tour-id").value = ""
          loadTours()
        } catch (error) {
          showMessage(error.message, true)
        }
      })
    }

    // Handle search form submission
    if (searchForm) {
      searchForm.addEventListener("submit", (e) => {
        e.preventDefault()

        const formData = new FormData(searchForm)
        const filters = {}

        for (const [key, value] of formData.entries()) {
          if (value) {
            filters[key] = value
          }
        }

        loadTours(filters)
      })
    }

    // Load tour packages with optional filters
    async function loadTours(filters = {}) {
      if (!toursList) return

      try {
        const response = await api.tours.getAll(filters)
        const tours = response.data.tours

        toursList.innerHTML = ""

        if (tours.length === 0) {
          toursList.innerHTML = '<tr><td colspan="7" class="text-center">No tour packages found</td></tr>'
          return
        }

        tours.forEach((tour) => {
          const row = document.createElement("tr")
          row.innerHTML = `
                        <td>${tour.tour_package_id}</td>
                        <td>${tour.tour_package_name}</td>
                        <td>${tour.destination_name}, ${tour.city}</td>
                        <td>$${Number.parseFloat(tour.price).toFixed(2)}</td>
                        <td>${tour.duration} days</td>
                        <td>
                            <span class="status-badge status-${tour.tour_status.toLowerCase()}">
                                ${tour.tour_status.charAt(0).toUpperCase() + tour.tour_status.slice(1)}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-tour" data-id="${tour.tour_package_id}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-tour" data-id="${tour.tour_package_id}">Delete</button>
                        </td>
                    `
          toursList.appendChild(row)
        })

        // Add event listeners for edit and delete buttons
        document.querySelectorAll(".edit-tour").forEach((button) => {
          button.addEventListener("click", function () {
            const tourId = this.getAttribute("data-id")
            editTour(tourId)
          })
        })

        document.querySelectorAll(".delete-tour").forEach((button) => {
          button.addEventListener("click", function () {
            const tourId = this.getAttribute("data-id")
            if (confirm("Are you sure you want to delete this tour package?")) {
              deleteTour(tourId)
            }
          })
        })
      } catch (error) {
        showMessage(error.message, true)
      }
    }

    // Edit tour package
    async function editTour(tourId) {
      try {
        const response = await api.tours.getById(tourId)
        const tour = response.data

        // Populate form with tour data
        document.getElementById("tour-form-title").textContent = "Edit Tour Package"
        document.getElementById("tour-id").value = tour.tour_package_id
        document.getElementById("tour-name").value = tour.tour_package_name
        document.getElementById("description").value = tour.description
        document.getElementById("destination-id").value = tour.destination_id
        document.getElementById("duration").value = tour.duration
        document.getElementById("price").value = tour.price
        document.getElementById("start-date").value = tour.start_date
        document.getElementById("end-date").value = tour.end_date
        document.getElementById("capacity").value = tour.capacity
        document.getElementById("tour-status").value = tour.tour_status
        document.getElementById("tour-language").value = tour.tour_language

        // Scroll to form
        tourForm.scrollIntoView({ behavior: "smooth" })
      } catch (error) {
        showMessage(error.message, true)
      }
    }

    // Delete tour package
    async function deleteTour(tourId) {
      try {
        await api.tours.delete(tourId)
        showMessage("Tour package deleted successfully")
        loadTours()
      } catch (error) {
        showMessage(error.message, true)
      }
    }
  }

  // Destination Management
  function initDestinationManagement() {
    const destinationForm = document.getElementById("destination-form")
    const destinationsList = document.getElementById("destinations-list")
    const searchForm = document.getElementById("search-form")

    // Load destinations
    loadDestinations()

    // Handle form submission for creating/updating destinations
    if (destinationForm) {
      destinationForm.addEventListener("submit", async (e) => {
        e.preventDefault()

        const formData = new FormData(destinationForm)
        const destinationId = formData.get("destination_id")

        try {
          let response
          if (destinationId) {
            // Update existing destination
            response = await api.destinations.update(formData)
            showMessage("Destination updated successfully")
          } else {
            // Create new destination
            response = await api.destinations.create(formData)
            showMessage("Destination created successfully")
          }

          // Reset form and reload destinations
          destinationForm.reset()
          document.getElementById("destination-form-title").textContent = "Add New Destination"
          document.getElementById("destination-id").value = ""
          loadDestinations()
        } catch (error) {
          showMessage(error.message, true)
        }
      })
    }

    // Handle search form submission
    if (searchForm) {
      searchForm.addEventListener("submit", (e) => {
        e.preventDefault()

        const formData = new FormData(searchForm)
        const filters = {}

        for (const [key, value] of formData.entries()) {
          if (value) {
            filters[key] = value
          }
        }

        loadDestinations(filters)
      })
    }

    // Load destinations with optional filters
    async function loadDestinations(filters = {}) {
      if (!destinationsList) return

      try {
        const response = await api.destinations.getAll(filters)
        const destinations = response.data.destinations

        destinationsList.innerHTML = ""

        if (destinations.length === 0) {
          destinationsList.innerHTML = '<tr><td colspan="5" class="text-center">No destinations found</td></tr>'
          return
        }

        destinations.forEach((destination) => {
          const row = document.createElement("tr")
          row.innerHTML = `
                        <td>${destination.destination_id}</td>
                        <td>${destination.destination_name}</td>
                        <td>${destination.city}</td>
                        <td>
                            <img src="${destination.image_url}" alt="${destination.destination_name}" width="100" height="60" style="object-fit: cover;">
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-destination" data-id="${destination.destination_id}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-destination" data-id="${destination.destination_id}">Delete</button>
                        </td>
                    `
          destinationsList.appendChild(row)
        })

        // Add event listeners for edit and delete buttons
        document.querySelectorAll(".edit-destination").forEach((button) => {
          button.addEventListener("click", function () {
            const destinationId = this.getAttribute("data-id")
            editDestination(destinationId)
          })
        })

        document.querySelectorAll(".delete-destination").forEach((button) => {
          button.addEventListener("click", function () {
            const destinationId = this.getAttribute("data-id")
            if (confirm("Are you sure you want to delete this destination?")) {
              deleteDestination(destinationId)
            }
          })
        })
      } catch (error) {
        showMessage(error.message, true)
      }
    }

    // Edit destination
    async function editDestination(destinationId) {
      try {
        const response = await api.destinations.getById(destinationId)
        const destination = response.data

        // Populate form with destination data
        document.getElementById("destination-form-title").textContent = "Edit Destination"
        document.getElementById("destination-id").value = destination.destination_id
        document.getElementById("destination-name").value = destination.destination_name
        document.getElementById("city").value = destination.city
        document.getElementById("description").value = destination.description

        // Scroll to form
        destinationForm.scrollIntoView({ behavior: "smooth" })
      } catch (error) {
        showMessage(error.message, true)
      }
    }

    // Delete destination
    async function deleteDestination(destinationId) {
      try {
        await api.destinations.delete(destinationId)
        showMessage("Destination deleted successfully")
        loadDestinations()
      } catch (error) {
        showMessage(error.message, true)
      }
    }
  }

  // Booking Management
  function initBookingManagement() {
    const bookingsList = document.getElementById("bookings-list")
    const searchForm = document.getElementById("search-form")

    // Load bookings
    loadBookings()

    // Handle search form submission
    if (searchForm) {
      searchForm.addEventListener("submit", (e) => {
        e.preventDefault()

        const formData = new FormData(searchForm)
        const filters = {}

        for (const [key, value] of formData.entries()) {
          if (value) {
            filters[key] = value
          }
        }

        loadBookings(filters)
      })
    }

    // Load bookings with optional filters
    async function loadBookings(filters = {}) {
      if (!bookingsList) return

      try {
        const response = await api.bookings.getAll(filters)
        const bookings = response.data.bookings

        bookingsList.innerHTML = ""

        if (bookings.length === 0) {
          bookingsList.innerHTML = '<tr><td colspan="7" class="text-center">No bookings found</td></tr>'
          return
        }

        bookings.forEach((booking) => {
          const row = document.createElement("tr")
          row.innerHTML = `
                        <td>${booking.booking_id}</td>
                        <td>${booking.tour_package_name}</td>
                        <td>${booking.destination_name}, ${booking.city}</td>
                        <td>${new Date(booking.booking_date).toLocaleDateString()}</td>
                        <td>$${Number.parseFloat(booking.total_price).toFixed(2)}</td>
                        <td>
                            <span class="status-badge status-${booking.booking_status.toLowerCase()}">
                                ${booking.booking_status.charAt(0).toUpperCase() + booking.booking_status.slice(1)}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary view-booking" data-id="${booking.booking_id}">View</button>
                            <button class="btn btn-sm btn-success update-status" data-id="${booking.booking_id}">Update</button>
                            <button class="btn btn-sm btn-danger delete-booking" data-id="${booking.booking_id}">Delete</button>
                        </td>
                    `
          bookingsList.appendChild(row)
        })

        // Add event listeners for buttons
        document.querySelectorAll(".view-booking").forEach((button) => {
          button.addEventListener("click", function () {
            const bookingId = this.getAttribute("data-id")
            viewBooking(bookingId)
          })
        })

        document.querySelectorAll(".update-status").forEach((button) => {
          button.addEventListener("click", function () {
            const bookingId = this.getAttribute("data-id")
            updateBookingStatus(bookingId)
          })
        })

        document.querySelectorAll(".delete-booking").forEach((button) => {
          button.addEventListener("click", function () {
            const bookingId = this.getAttribute("data-id")
            if (confirm("Are you sure you want to delete this booking?")) {
              deleteBooking(bookingId)
            }
          })
        })
      } catch (error) {
        showMessage(error.message, true)
      }
    }

    // View booking details
    async function viewBooking(bookingId) {
      try {
        const response = await api.bookings.getById(bookingId)
        const booking = response.data

        // Create modal with booking details
        const modalHtml = `
                    <div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Booking #${booking.booking_id} Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Tour Information</h6>
                                            <p><strong>Package:</strong> ${booking.tour_package_name}</p>
                                            <p><strong>Destination:</strong> ${booking.destination_name}, ${booking.city}</p>
                                            <p><strong>Duration:</strong> ${booking.number_of_days} days</p>
                                            <p><strong>Travel Date:</strong> ${new Date(booking.travel_date).toLocaleDateString()}</p>
                                            <p><strong>Number of Travelers:</strong> ${booking.number_of_travelers}</p>
                                            <p><strong>Total Price:</strong> $${Number.parseFloat(booking.total_price).toFixed(2)}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Customer Information</h6>
                                            <p><strong>Name:</strong> ${booking.first_name} ${booking.last_name}</p>
                                            <p><strong>Email:</strong> ${booking.email}</p>
                                            <p><strong>Phone:</strong> ${booking.phone}</p>
                                            <p><strong>Emergency Contact:</strong> ${booking.emergency_contact}</p>
                                            <p><strong>Special Requests:</strong> ${booking.special_request || "None"}</p>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6>Booking Status</h6>
                                            <p><strong>Status:</strong> 
                                                <span class="status-badge status-${booking.booking_status.toLowerCase()}">
                                                    ${booking.booking_status.charAt(0).toUpperCase() + booking.booking_status.slice(1)}
                                                </span>
                                            </p>
                                            <p><strong>Booking Date:</strong> ${new Date(booking.booking_date).toLocaleString()}</p>
                                            <p><strong>Tour Guide:</strong> ${booking.tour_guide || "Not assigned"}</p>
                                            <p><strong>Check-in Status:</strong> ${booking.check_in_status ? "Checked In" : "Not Checked In"}</p>
                                            <p><strong>Check-out Status:</strong> ${booking.check_out_status ? "Checked Out" : "Not Checked Out"}</p>
                                            ${booking.booking_status === "cancelled" ? `<p><strong>Cancellation Reason:</strong> ${booking.cancellation_reason || "Not specified"}</p>` : ""}
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `

        // Add modal to document
        const modalContainer = document.createElement("div")
        modalContainer.innerHTML = modalHtml
        document.body.appendChild(modalContainer)

        // Show modal
        $("#bookingDetailsModal").modal("show")

        // Remove modal from DOM when closed
        $("#bookingDetailsModal").on("hidden.bs.modal", () => {
          modalContainer.remove()
        })
      } catch (error) {
        showMessage(error.message, true)
      }
    }

    // Update booking status
    async function updateBookingStatus(bookingId) {
      try {
        const response = await api.bookings.getById(bookingId)
        const booking = response.data

        // Create modal with status update form
        const modalHtml = `
                    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Booking #${booking.booking_id} Status</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="update-status-form">
                                        <input type="hidden" name="booking_id" value="${booking.booking_id}">
                                        <div class="form-group">
                                            <label for="booking-status">Booking Status</label>
                                            <select class="form-control" id="booking-status" name="booking_status" required>
                                                <option value="pending" ${booking.booking_status === "pending" ? "selected" : ""}>Pending</option>
                                                <option value="confirmed" ${booking.booking_status === "confirmed" ? "selected" : ""}>Confirmed</option>
                                                <option value="cancelled" ${booking.booking_status === "cancelled" ? "selected" : ""}>Cancelled</option>
                                                <option value="completed" ${booking.booking_status === "completed" ? "selected" : ""}>Completed</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="tour-guide">Tour Guide</label>
                                            <input type="text" class="form-control" id="tour-guide" name="tour_guide" value="${booking.tour_guide || ""}">
                                        </div>
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check-in-status" name="check_in_status" value="1" ${booking.check_in_status ? "checked" : ""}>
                                                <label class="form-check-label" for="check-in-status">
                                                    Checked In
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check-out-status" name="check_out_status" value="1" ${booking.check_out_status ? "checked" : ""}>
                                                <label class="form-check-label" for="check-out-status">
                                                    Checked Out
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group" id="cancellation-reason-group" style="display: ${booking.booking_status === "cancelled" ? "block" : "none"}">
                                            <label for="cancellation-reason">Cancellation Reason</label>
                                            <textarea class="form-control" id="cancellation-reason" name="cancellation_reason" rows="3">${booking.cancellation_reason || ""}</textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="save-status">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `

        // Add modal to document
        const modalContainer = document.createElement("div")
        modalContainer.innerHTML = modalHtml
        document.body.appendChild(modalContainer)

        // Show modal
        $("#updateStatusModal").modal("show")

        // Show/hide cancellation reason based on status
        document.getElementById("booking-status").addEventListener("change", function () {
          const cancellationReasonGroup = document.getElementById("cancellation-reason-group")
          cancellationReasonGroup.style.display = this.value === "cancelled" ? "block" : "none"
        })

        // Handle form submission
        document.getElementById("save-status").addEventListener("click", async () => {
          const form = document.getElementById("update-status-form")
          const formData = new FormData(form)

          try {
            await api.bookings.update(formData)
            showMessage("Booking status updated successfully")
            $("#updateStatusModal").modal("hide")
            loadBookings()
          } catch (error) {
            showMessage(error.message, true)
          }
        })

        // Remove modal from DOM when closed
        $("#updateStatusModal").on("hidden.bs.modal", () => {
          modalContainer.remove()
        })
      } catch (error) {
        showMessage(error.message, true)
      }
    }

    // Delete booking
    async function deleteBooking(bookingId) {
      try {
        await api.bookings.delete(bookingId)
        showMessage("Booking deleted successfully")
        loadBookings()
      } catch (error) {
        showMessage(error.message, true)
      }
    }
  }

  // Dashboard initialization
  function initDashboard() {
    // Load dashboard statistics and charts
    loadDashboardStats()

    async function loadDashboardStats() {
      // This would typically fetch data from an API endpoint
      // For now, we'll use placeholder data

      // Example: Create a bookings chart
      const bookingsChart = document.getElementById("bookings-chart")
      if (bookingsChart) {
        new Chart(bookingsChart, {
          type: "line",
          data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [
              {
                label: "Bookings",
                data: [65, 59, 80, 81, 56, 55, 40, 45, 60, 70, 85, 90],
                fill: false,
                borderColor: "#2563eb",
                tension: 0.1,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
          },
        })
      }

      // Example: Create a revenue chart
      const revenueChart = document.getElementById("revenue-chart")
      if (revenueChart) {
        new Chart(revenueChart, {
          type: "bar",
          data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [
              {
                label: "Revenue ($)",
                data: [12500, 11000, 15000, 16000, 10500, 9800, 8000, 9500, 12000, 14000, 17000, 19000],
                backgroundColor: "#10b981",
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
          },
        })
      }

      // Example: Create a destinations chart
      const destinationsChart = document.getElementById("destinations-chart")
      if (destinationsChart) {
        new Chart(destinationsChart, {
          type: "pie",
          data: {
            labels: ["Paris", "Bali", "Tokyo", "New York", "Cairo", "Sydney"],
            datasets: [
              {
                data: [30, 25, 20, 15, 5, 5],
                backgroundColor: ["#2563eb", "#10b981", "#f59e0b", "#ec4899", "#8b5cf6", "#ef4444"],
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
          },
        })
      }
    }
  }

  // User Management
  function initUserManagement() {
    // Similar implementation to other management functions
    // This would handle CRUD operations for users
  }
})


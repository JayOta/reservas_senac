// Sistema de Reserva de Ocupação - Senac
// JavaScript principal com funcionalidades interativas

document.addEventListener("DOMContentLoaded", function () {
  // Inicializar componentes
  initModals();
  initCalendar();
  initForms();
  initTooltips();
});

// Gerenciamento de Modais
function initModals() {
  const modals = document.querySelectorAll(".modal");
  const modalTriggers = document.querySelectorAll("[data-modal]");
  const modalCloses = document.querySelectorAll(".close, .modal-close");

  // Abrir modal
  modalTriggers.forEach((trigger) => {
    trigger.addEventListener("click", function (e) {
      e.preventDefault();
      const modalId = this.getAttribute("data-modal");
      const modal = document.getElementById(modalId);
      if (modal) {
        showModal(modal);
      }
    });
  });

  // Fechar modal
  modalCloses.forEach((close) => {
    close.addEventListener("click", function () {
      const modal = this.closest(".modal");
      if (modal) {
        hideModal(modal);
      }
    });
  });

  // Fechar modal clicando fora
  modals.forEach((modal) => {
    modal.addEventListener("click", function (e) {
      if (e.target === this) {
        hideModal(this);
      }
    });
  });

  // Fechar modal com ESC
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      const openModal = document.querySelector(".modal.show");
      if (openModal) {
        hideModal(openModal);
      }
    }
  });
}

function showModal(modal) {
  modal.classList.add("show");
  document.body.style.overflow = "hidden";
}

function hideModal(modal) {
  modal.classList.remove("show");
  document.body.style.overflow = "";
}

// Funcionalidades do Calendário
function initCalendar() {
  const calendarContainer = document.getElementById("calendar");
  if (!calendarContainer) return;

  // Navegação do calendário
  const prevBtn = document.querySelector(".calendar-prev");
  const nextBtn = document.querySelector(".calendar-next");
  const currentMonth = document.querySelector(".calendar-current-month");

  if (prevBtn) {
    prevBtn.addEventListener("click", function () {
      navigateCalendar(-1);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", function () {
      navigateCalendar(1);
    });
  }

  // Clique em slots de tempo
  const timeSlots = document.querySelectorAll(".time-slot.available");
  timeSlots.forEach((slot) => {
    slot.addEventListener("click", function () {
      const datetime = this.getAttribute("data-datetime");
      openReservationModal(datetime);
    });
  });
}

function navigateCalendar(direction) {
  const currentDate = new Date();
  const year = currentDate.getFullYear();
  const month = currentDate.getMonth() + direction;

  // Recarregar calendário via AJAX
  loadCalendar(year, month);
}

function loadCalendar(year, month) {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", `ajax/calendar.php?year=${year}&month=${month}`, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      const calendarContainer = document.getElementById("calendar");
      if (calendarContainer) {
        calendarContainer.innerHTML = xhr.responseText;
        initCalendar(); // Reinicializar eventos
      }
    }
  };
  xhr.send();
}

function openReservationModal(datetime) {
  const modal = document.getElementById("reservationModal");
  const datetimeInput = document.getElementById("reservation_datetime");

  if (modal && datetimeInput) {
    datetimeInput.value = datetime;
    showModal(modal);
  }
}

// Validação de Formulários
function initForms() {
  const forms = document.querySelectorAll("form[data-validate]");

  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!validateForm(this)) {
        e.preventDefault();
      }
    });
  });

  // Validação em tempo real
  const inputs = document.querySelectorAll(
    "input[required], textarea[required]"
  );
  inputs.forEach((input) => {
    input.addEventListener("blur", function () {
      validateField(this);
    });
  });
}

function validateForm(form) {
  let isValid = true;
  const inputs = form.querySelectorAll(
    "input[required], textarea[required], select[required]"
  );

  inputs.forEach((input) => {
    if (!validateField(input)) {
      isValid = false;
    }
  });

  return isValid;
}

function validateField(field) {
  const value = field.value.trim();
  const type = field.type;
  let isValid = true;
  let message = "";

  // Remover mensagens de erro anteriores
  removeFieldError(field);

  // Validações básicas
  if (field.hasAttribute("required") && !value) {
    isValid = false;
    message = "Este campo é obrigatório";
  } else if (type === "email" && value && !isValidEmail(value)) {
    isValid = false;
    message = "Email inválido";
  } else if (type === "password" && value && value.length < 6) {
    isValid = false;
    message = "Senha deve ter pelo menos 6 caracteres";
  } else if (field.name === "confirm_password") {
    const password = document.querySelector('input[name="senha"]');
    if (password && value !== password.value) {
      isValid = false;
      message = "Senhas não coincidem";
    }
  }

  // Validação de data/hora
  if (field.type === "datetime-local" && value) {
    const selectedDate = new Date(value);
    const now = new Date();
    if (selectedDate <= now) {
      isValid = false;
      message = "Data deve ser futura";
    }
  }

  if (!isValid) {
    showFieldError(field, message);
  } else {
    showFieldSuccess(field);
  }

  return isValid;
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function showFieldError(field, message) {
  field.classList.add("is-invalid");
  field.classList.remove("is-valid");

  const errorDiv = document.createElement("div");
  errorDiv.className = "field-error text-danger mt-1";
  errorDiv.textContent = message;

  field.parentNode.appendChild(errorDiv);
}

function showFieldSuccess(field) {
  field.classList.add("is-valid");
  field.classList.remove("is-invalid");
}

function removeFieldError(field) {
  field.classList.remove("is-invalid", "is-valid");
  const errorDiv = field.parentNode.querySelector(".field-error");
  if (errorDiv) {
    errorDiv.remove();
  }
}

// Tooltips
function initTooltips() {
  const tooltipElements = document.querySelectorAll("[data-tooltip]");

  tooltipElements.forEach((element) => {
    element.addEventListener("mouseenter", showTooltip);
    element.addEventListener("mouseleave", hideTooltip);
  });
}

function showTooltip(e) {
  const text = e.target.getAttribute("data-tooltip");
  const tooltip = document.createElement("div");
  tooltip.className = "tooltip";
  tooltip.textContent = text;
  tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
        pointer-events: none;
    `;

  document.body.appendChild(tooltip);

  const rect = e.target.getBoundingClientRect();
  tooltip.style.left =
    rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + "px";
  tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + "px";
}

function hideTooltip() {
  const tooltip = document.querySelector(".tooltip");
  if (tooltip) {
    tooltip.remove();
  }
}

// Utilitários AJAX
function makeRequest(url, method = "GET", data = null) {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          try {
            const response = JSON.parse(xhr.responseText);
            resolve(response);
          } catch (e) {
            resolve(xhr.responseText);
          }
        } else {
          reject(new Error("Request failed: " + xhr.status));
        }
      }
    };

    xhr.send(data);
  });
}

// Funções específicas do sistema
function approveReservation(id) {
  if (confirm("Tem certeza que deseja aprovar esta reserva?")) {
    makeRequest("ajax/approve_reservation.php", "POST", `id=${id}`)
      .then((response) => {
        if (response.success) {
          showAlert("Reserva aprovada com sucesso!", "success");
          location.reload();
        } else {
          showAlert("Erro ao aprovar reserva: " + response.message, "danger");
        }
      })
      .catch((error) => {
        showAlert("Erro ao aprovar reserva: " + error.message, "danger");
      });
  }
}

function rejectReservation(id) {
  if (confirm("Tem certeza que deseja recusar esta reserva?")) {
    makeRequest("ajax/reject_reservation.php", "POST", `id=${id}`)
      .then((response) => {
        if (response.success) {
          showAlert("Reserva recusada com sucesso!", "success");
          location.reload();
        } else {
          showAlert("Erro ao recusar reserva: " + response.message, "danger");
        }
      })
      .catch((error) => {
        showAlert("Erro ao recusar reserva: " + error.message, "danger");
      });
  }
}

function deleteReservation(id) {
  if (confirm("Tem certeza que deseja excluir esta reserva?")) {
    makeRequest("ajax/delete_reservation.php", "POST", `id=${id}`)
      .then((response) => {
        if (response.success) {
          showAlert("Reserva excluída com sucesso!", "success");
          location.reload();
        } else {
          showAlert("Erro ao excluir reserva: " + response.message, "danger");
        }
      })
      .catch((error) => {
        showAlert("Erro ao excluir reserva: " + error.message, "danger");
      });
  }
}

// Sistema de Alertas
function showAlert(message, type = "info") {
  const alertContainer =
    document.getElementById("alert-container") || createAlertContainer();

  const alert = document.createElement("div");
  alert.className = `alert alert-${type} alert-dismissible fade show`;
  alert.innerHTML = `
        ${message}
        <button type="button" class="close" onclick="this.parentElement.remove()">
            <span>&times;</span>
        </button>
    `;

  alertContainer.appendChild(alert);

  // Auto-remover após 5 segundos
  setTimeout(() => {
    if (alert.parentNode) {
      alert.remove();
    }
  }, 5000);
}

function createAlertContainer() {
  const container = document.createElement("div");
  container.id = "alert-container";
  container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    `;
  document.body.appendChild(container);
  return container;
}

// Função para formatar data
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("pt-BR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

// Função para formatar status
function formatStatus(status) {
  const statusMap = {
    pendente: { text: "Pendente", class: "warning" },
    aprovada: { text: "Aprovada", class: "success" },
    recusada: { text: "Recusada", class: "danger" },
  };

  const statusInfo = statusMap[status] || { text: status, class: "secondary" };
  return `<span class="badge badge-${statusInfo.class}">${statusInfo.text}</span>`;
}

// Loading states
function showLoading(element) {
  element.innerHTML = '<div class="loading"></div> Carregando...';
  element.disabled = true;
}

function hideLoading(element, originalText) {
  element.innerHTML = originalText;
  element.disabled = false;
}

// Confirmação de ações
function confirmAction(message, callback) {
  if (confirm(message)) {
    callback();
  }
}

// Exportar funções globais
window.showModal = showModal;
window.hideModal = hideModal;
window.approveReservation = approveReservation;
window.rejectReservation = rejectReservation;
window.deleteReservation = deleteReservation;
window.showAlert = showAlert;
window.formatDate = formatDate;
window.formatStatus = formatStatus;

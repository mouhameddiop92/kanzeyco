/**
 * Scripts JavaScript pour le dashboard administrateur
 */

// Toggle sidebar
document.addEventListener('DOMContentLoaded', function() {
    const toggleSidebarBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    
    if (toggleSidebarBtn && sidebar) {
        toggleSidebarBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
    
    // Gestion responsive
    if (window.innerWidth <= 768) {
        if (sidebar) {
            sidebar.classList.remove('collapsed');
        }
    }
    
    // Toggle sidebar sur mobile
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            if (sidebar) {
                sidebar.classList.remove('collapsed');
            }
        }
    });
});

// Confirmation de suppression
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
    return confirm(message);
}

// Afficher/masquer les notifications
document.addEventListener('DOMContentLoaded', function() {
    const notificationDropdowns = document.querySelectorAll('.notification-dropdown');
    
    notificationDropdowns.forEach(dropdown => {
        dropdown.addEventListener('show.bs.dropdown', function() {
            // Ici, on pourrait marquer les notifications comme lues
        });
    });
});

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });
});

// Validation de formulaire
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    if (form.checkValidity()) {
        return true;
    } else {
        form.reportValidity();
        return false;
    }
}

// Fonction pour afficher un message de succès
function showSuccess(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show';
    alertDiv.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        contentWrapper.insertBefore(alertDiv, contentWrapper.firstChild);
        
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    }
}

// Fonction pour afficher un message d'erreur
function showError(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
    alertDiv.innerHTML = `
        <i class="fas fa-exclamation-circle me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        contentWrapper.insertBefore(alertDiv, contentWrapper.firstChild);
    }
}

// Fonction pour charger du contenu via AJAX (si nécessaire)
function loadContent(url, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
        })
        .catch(error => {
            container.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement du contenu.</div>';
            console.error('Erreur:', error);
        });
}

// Initialisation des tooltips Bootstrap (si nécessaire)
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Initialisation des popovers Bootstrap (si nécessaire)
document.addEventListener('DOMContentLoaded', function() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Gestion de la recherche en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const searchInputs = document.querySelectorAll('[id$="search"], [id*="Search"]');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tableId = e.target.getAttribute('data-table');
            
            if (tableId) {
                const table = document.getElementById(tableId);
                if (table) {
                    const rows = table.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                }
            }
        });
    });
});

// Animation des cartes de statistiques au chargement
document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(function() {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Gestion du changement de période dans les graphiques
function updateChartPeriod(chart, period) {
    // Cette fonction peut être personnalisée selon les besoins
    console.log('Mise à jour du graphique pour la période:', period);
}


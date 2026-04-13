class AuthApp {
    constructor() {
        this.init();
    }

    init() {
        this.setupLoginForm();
    }

    setupLoginForm() {
        const form = document.getElementById('loginForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleLogin(e));
        }
    }

    async handleLogin(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const credentials = {
            identifiant: formData.get('identifiant'),
            password: formData.get('password')
        };

        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(credentials)
            });

            const data = await response.json();

            if (response.ok) {
                // Stocker le token et rediriger
                localStorage.setItem('auth_token', data.token);
                window.location.href = '/dashboard';
            } else {
                this.showError(data.error || 'Erreur de connexion');
            }
        } catch (error) {
            this.showError('Erreur réseau');
        }
    }

    showError(message) {
        // Ajoutez une belle notification d'erreur
        alert('Erreur: ' + message);
    }
}
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Validation des mots de passe
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;

    if (password !== confirmPassword) {
        alert('Les mots de passe ne correspondent pas!');
        return;
    }

    // Si validation réussie, on peut soumettre le formulaire
    alert('Formulaire validé avec succès!');
    // this.submit(); // Décommentez pour une soumission réelle
});
// Démarrer l'application quand la page est chargée
document.addEventListener('DOMContentLoaded', () => {
    new AuthApp();
});

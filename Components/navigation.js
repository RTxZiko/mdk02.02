class Navigation {
    constructor() {
        this.sidebarItems = document.querySelectorAll('.sidebar-item');
        this.screens = document.querySelectorAll('.screen');
        this.init();
    }

    init() {
        this.setupSidebarNavigation();
        this.setupDayNavigation();
        this.setupMuscleGroupNavigation();
    }

    setupSidebarNavigation() {
        this.sidebarItems.forEach(item => {
            item.addEventListener('click', () => {
                this.closeAllScreens();
                item.classList.add('active');
                
                const screenId = item.getAttribute('data-screen');
                if (screenId) {
                    const screen = document.getElementById(screenId);
                    if (screen) {
                        screen.classList.add('active');
                    }
                }
            });
        });

        if (this.sidebarItems.length > 0) {
            this.sidebarItems[0].click();
        }
    }

    setupDayNavigation() {
        const dayCards = document.querySelectorAll('.day-card');
        const workoutsSidebarItem = document.querySelector('[data-screen="workouts"]');
        
        dayCards.forEach(card => {
            card.addEventListener('click', () => {
                const dayName = card.getAttribute('data-day');
                if (workoutsSidebarItem) {
                    this.closeAllScreens();
                    workoutsSidebarItem.classList.add('active');
                    
                    const workoutsScreen = document.getElementById('workouts');
                    if (workoutsScreen) {
                        workoutsScreen.classList.add('active');
                        
                        const selectedDaySpan = workoutsScreen.querySelector('#selected-day');
                        if (selectedDaySpan) {
                            selectedDaySpan.textContent = `- ${dayName}`;
                        }
                    }
                }
            });
        });
    }

    setupMuscleGroupNavigation() {
        const muscleButtons = document.querySelectorAll('.muscle-btn');
        const exercisesSidebarItem = document.querySelector('[data-screen="exercises"]');
        
        muscleButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const muscleName = btn.getAttribute('data-muscle');
                if (exercisesSidebarItem) {
                    this.closeAllScreens();
                    exercisesSidebarItem.classList.add('active');
                    
                    const exercisesScreen = document.getElementById('exercises');
                    if (exercisesScreen) {
                        exercisesScreen.classList.add('active');
                        const muscleName = btn.querySelector('span').textContent;
                        exercisesScreen.innerHTML = `<h2 class="content-title">Упражнения - ${muscleName}</h2><p style="color: #111; font-size: 16px;">Здесь будут упражнения для ${muscleName.toLowerCase()}</p>`;
                    }
                }
            });
        });
    }

    closeAllScreens() {
        this.sidebarItems.forEach(el => el.classList.remove('active'));
        this.screens.forEach(el => el.classList.remove('active'));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    new Navigation();
});

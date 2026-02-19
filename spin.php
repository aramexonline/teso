<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélecteur de Montant</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --text-color: #333;
            --highlight-color: #e74c3c;
            --background-color: #f9f9f9;
            --wheel-background: #fff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--text-color);
        }
        
        .container {
            width: 90%;
            max-width: 500px;
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background-color: var(--wheel-background);
        }
        
        h1 {
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .wheel-container {
            position: relative;
            height: 300px;
            overflow: hidden;
            margin: 2rem 0;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .wheel {
            position: absolute;
            width: 100%;
            transition: transform 3s cubic-bezier(0.165, 0.84, 0.44, 1);
            transform: translateY(0);
        }
        
        .wheel-item {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--text-color);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .wheel-item.selected {
            color: var(--highlight-color);
            font-weight: 700;
            font-size: 1.8rem;
        }
        
        .selector {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            height: 60px;
            background-color: rgba(52, 152, 219, 0.1);
            border-top: 2px solid var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            pointer-events: none;
            z-index: 2;
        }
        
        .controls {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }
        
        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .selected-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--highlight-color);
            margin: 1.5rem 0;
            transition: all 0.3s ease;
        }
        
        .euro-sign {
            font-size: 1.5rem;
            margin-left: 5px;
        }
        
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 1.5rem;
            }
            
            .wheel-item {
                font-size: 1.3rem;
            }
            
            .wheel-item.selected {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sélectionnez un montant</h1>
        
        <div class="selected-value">
            <span id="selected-amount">1500</span><span class="euro-sign">€</span>
        </div>
        
        <div class="wheel-container">
            <div class="selector"></div>
            <div class="wheel" id="wheel"></div>
        </div>
        
        <div class="controls">
            <button id="spin-btn">Tourner la roue</button>
            <button id="select-btn">Sélectionner</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wheel = document.getElementById('wheel');
            const spinBtn = document.getElementById('spin-btn');
            const selectBtn = document.getElementById('select-btn');
            const selectedAmount = document.getElementById('selected-amount');
            
            // Générer les montants de 500€ à 3000€ par incréments de 100€
            const amounts = [];
            for (let i = 500; i <= 3000; i += 100) {
                amounts.push(i);
            }
            
            // Créer les éléments de la roue
            function createWheelItems() {
                wheel.innerHTML = '';
                
                // Ajouter des éléments vides au début pour le centrage
                for (let i = 0; i < 2; i++) {
                    const emptyItem = document.createElement('div');
                    emptyItem.className = 'wheel-item';
                    emptyItem.innerHTML = '&nbsp;';
                    wheel.appendChild(emptyItem);
                }
                
                // Ajouter les montants
                amounts.forEach(amount => {
                    const item = document.createElement('div');
                    item.className = 'wheel-item';
                    item.textContent = amount;
                    item.dataset.value = amount;
                    wheel.appendChild(item);
                });
                
                // Ajouter des éléments vides à la fin pour le centrage
                for (let i = 0; i < 2; i++) {
                    const emptyItem = document.createElement('div');
                    emptyItem.className = 'wheel-item';
                    emptyItem.innerHTML = '&nbsp;';
                    wheel.appendChild(emptyItem);
                }
            }
            
            createWheelItems();
            
            // Positionner la roue initialement
            const itemHeight = 60;
            const initialIndex = 10; // Commencer à 1500€ (index 10 = 1500€)
            wheel.style.transform = `translateY(${-initialIndex * itemHeight + itemHeight * 2}px)`;
            
            // Mettre à jour la valeur sélectionnée
            function updateSelectedValue() {
                const centerPosition = -parseInt(wheel.style.transform.replace('translateY(', '').replace('px)', ''));
                const selectedIndex = Math.round(centerPosition / itemHeight) - 2;
                
                if (selectedIndex >= 0 && selectedIndex < amounts.length) {
                    selectedAmount.textContent = amounts[selectedIndex];
                    
                    // Mettre à jour la classe selected
                    const items = wheel.querySelectorAll('.wheel-item');
                    items.forEach(item => item.classList.remove('selected'));
                    
                    // +2 pour compenser les éléments vides au début
                    if (items[selectedIndex + 2]) {
                        items[selectedIndex + 2].classList.add('selected');
                    }
                }
            }
            
            // Initialiser la valeur sélectionnée
            setTimeout(updateSelectedValue, 100);
            
            // Faire tourner la roue
            spinBtn.addEventListener('click', function() {
                const randomIndex = Math.floor(Math.random() * amounts.length);
                const targetPosition = -randomIndex * itemHeight + itemHeight * 2;
                
                wheel.style.transition = 'transform 3s cubic-bezier(0.165, 0.84, 0.44, 1)';
                wheel.style.transform = `translateY(${targetPosition}px)`;
                
                setTimeout(updateSelectedValue, 3000);
            });
            
            // Sélectionner le montant
            selectBtn.addEventListener('click', function() {
                const amount = selectedAmount.textContent;
                alert(`Vous avez sélectionné ${amount}€`);
                // Ici vous pouvez ajouter du code pour traiter la sélection
            });
            
            // Permettre le défilement manuel de la roue
            let startY, currentTranslate, isDragging = false;
            
            wheel.addEventListener('touchstart', touchStart);
            wheel.addEventListener('touchmove', touchMove);
            wheel.addEventListener('touchend', touchEnd);
            wheel.addEventListener('mousedown', touchStart);
            window.addEventListener('mousemove', touchMove);
            window.addEventListener('mouseup', touchEnd);
            
            function touchStart(event) {
                startY = getPositionY(event);
                isDragging = true;
                currentTranslate = parseInt(wheel.style.transform.replace('translateY(', '').replace('px)', ''));
                wheel.style.transition = 'none';
            }
            
            function touchMove(event) {
                if (isDragging) {
                    const currentY = getPositionY(event);
                    const diff = currentY - startY;
                    wheel.style.transform = `translateY(${currentTranslate + diff}px)`;
                }
            }
            
            function touchEnd() {
                if (!isDragging) return;
                isDragging = false;
                
                const currentPosition = parseInt(wheel.style.transform.replace('translateY(', '').replace('px)', ''));
                const snappedPosition = Math.round(currentPosition / itemHeight) * itemHeight;
                
                wheel.style.transition = 'transform 0.3s ease-out';
                wheel.style.transform = `translateY(${snappedPosition}px)`;
                
                setTimeout(updateSelectedValue, 300);
            }
            
            function getPositionY(event) {
                return event.type.includes('mouse') ? event.clientY : event.touches[0].clientY;
            }
        });
    </script>
</body>
</html>
#!/bin/bash

# CESIZen - Script de démarrage Linux/macOS

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
GRAY='\033[0;90m'
NC='\033[0m'

echo ""
echo -e "  ${GREEN}CESI${NC}${YELLOW}Zen${NC} - Démarrage de l'application"
echo -e "  ${GRAY}----------------------------------------${NC}"
echo ""

# ── PHP ───────────────────────────────────────────────────────────────────────
printf "${CYAN}[1/6] Vérification PHP...${NC}"
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    echo -e " ${GREEN}OK (PHP $PHP_VERSION)${NC}"
else
    echo -e " ${RED}ERREUR${NC}"
    echo -e "      PHP n'est pas installé."
    echo -e "      Linux : sudo apt install php8.2"
    echo -e "      Mac   : brew install php"
    exit 1
fi

# ── Composer ──────────────────────────────────────────────────────────────────
printf "${CYAN}[2/6] Vérification Composer...${NC}"
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer -V 2>/dev/null | awk '{print $3}')
    echo -e " ${GREEN}OK (Composer $COMPOSER_VERSION)${NC}"
else
    echo -e " ${RED}ERREUR${NC}"
    echo -e "      Composer n'est pas installé : https://getcomposer.org/"
    exit 1
fi

# ── Symfony CLI ───────────────────────────────────────────────────────────────
printf "${CYAN}[3/6] Vérification Symfony CLI...${NC}"
if command -v symfony &> /dev/null; then
    echo -e " ${GREEN}OK${NC}"
else
    echo -e " ${RED}ERREUR${NC}"
    echo -e "      Symfony CLI n'est pas installé : https://symfony.com/download"
    exit 1
fi

# ── Dépendances Composer ──────────────────────────────────────────────────────
printf "${CYAN}[4/6] Vérification des dépendances...${NC}"
if [ ! -d "$SCRIPT_DIR/vendor" ]; then
    echo ""
    echo -e "      ${YELLOW}Installation des dépendances (première fois)...${NC}"
    composer install --no-interaction --quiet
    echo -e "      ${GREEN}Dépendances installées.${NC}"
else
    echo -e " ${GREEN}OK (vendor/ présent)${NC}"
fi

# ── MySQL ─────────────────────────────────────────────────────────────────────
printf "${CYAN}[5/6] Vérification MySQL...${NC}"

mysql_check() {
    (echo > /dev/tcp/127.0.0.1/3306) 2>/dev/null
    return $?
}

if mysql_check; then
    echo -e " ${GREEN}OK${NC}"
else
    echo -e " ${YELLOW}Arrêté, tentative de démarrage...${NC}"

    STARTED=false

    # macOS - Homebrew
    if command -v brew &> /dev/null && brew services list 2>/dev/null | grep -q mysql; then
        brew services start mysql &>/dev/null
        sleep 3
        mysql_check && STARTED=true
    fi

    # Linux - systemd
    if [ "$STARTED" = false ] && command -v systemctl &> /dev/null; then
        for SVC in mysql mysql8 mariadb mysqld; do
            sudo systemctl start $SVC &>/dev/null && sleep 3
            mysql_check && STARTED=true && break
        done
    fi

    # Linux - service
    if [ "$STARTED" = false ] && command -v service &> /dev/null; then
        sudo service mysql start &>/dev/null
        sleep 3
        mysql_check && STARTED=true
    fi

    if [ "$STARTED" = true ]; then
        echo -e "      ${GREEN}MySQL démarré.${NC}"
    else
        echo -e ""
        echo -e "      ${RED}Impossible de démarrer MySQL automatiquement.${NC}"
        echo -e "      ${YELLOW}Démarrez MySQL manuellement puis appuyez sur Entrée.${NC}"
        read -r
    fi
fi

# ── Lancement du serveur ──────────────────────────────────────────────────────
echo -e "${CYAN}[6/6] Démarrage du serveur Symfony...${NC}"
echo ""

# Lancer le serveur en arrière-plan
symfony server:start --no-tls &>/dev/null &
SERVER_PID=$!

# Attendre que le serveur démarre
sleep 3

# Détecter le port réel
SERVER_URL="http://127.0.0.1:8000"
STATUS_OUTPUT=$(symfony server:status --no-ansi 2>/dev/null)

if echo "$STATUS_OUTPUT" | grep -oE "http://127\.0\.0\.1:[0-9]+" &>/dev/null; then
    SERVER_URL=$(echo "$STATUS_OUTPUT" | grep -oE "http://127\.0\.0\.1:[0-9]+" | head -1)
fi

echo -e "  ${GREEN}Application disponible sur : $SERVER_URL${NC}"
echo -e "  ${GRAY}Appuyez sur Ctrl+C pour arrêter le serveur.${NC}"
echo ""

# Ouvrir le navigateur sur le bon port
(sleep 1 && (xdg-open "$SERVER_URL" 2>/dev/null || open "$SERVER_URL" 2>/dev/null)) &

# Attacher au processus serveur pour voir les logs
wait $SERVER_PID
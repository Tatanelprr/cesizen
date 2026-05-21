#!/bin/bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
GRAY='\033[0;90m'
NC='\033[0m'

echo ""
echo -e "  ${GREEN}CESI${NC}${YELLOW}Zen${NC} - Demarrage de l'application"
echo -e "  ${GRAY}----------------------------------------${NC}"
echo ""

# ── PHP ───────────────────────────────────────────────────────────────────────
printf "${CYAN}[1/6] Verification PHP...${NC}"
if command -v php &> /dev/null; then
    echo -e " ${GREEN}OK (PHP $(php -r 'echo PHP_VERSION;'))${NC}"
else
    echo -e " ${RED}ERREUR — Installez PHP : sudo apt install php8.2 (Linux) / brew install php (Mac)${NC}"
    exit 1
fi

# ── Composer ──────────────────────────────────────────────────────────────────
printf "${CYAN}[2/6] Verification Composer...${NC}"
if command -v composer &> /dev/null; then
    echo -e " ${GREEN}OK${NC}"
else
    echo -e " ${RED}ERREUR — https://getcomposer.org/${NC}"
    exit 1
fi

# ── Symfony CLI ───────────────────────────────────────────────────────────────
printf "${CYAN}[3/6] Verification Symfony CLI...${NC}"
if command -v symfony &> /dev/null; then
    echo -e " ${GREEN}OK${NC}"
else
    echo -e " ${RED}ERREUR — https://symfony.com/download${NC}"
    exit 1
fi

# ── Dependances ───────────────────────────────────────────────────────────────
printf "${CYAN}[4/6] Verification des dependances...${NC}"
if [ ! -d "$SCRIPT_DIR/vendor" ]; then
    echo ""
    echo -e "      ${YELLOW}Installation des dependances (premiere fois)...${NC}"
    composer install --no-interaction --quiet
    echo -e "      ${GREEN}Dependances installees.${NC}"
else
    echo -e " ${GREEN}OK (vendor/ present)${NC}"
fi

# ── MySQL ─────────────────────────────────────────────────────────────────────
printf "${CYAN}[5/6] Verification MySQL...${NC}"

mysql_check() {
    (echo > /dev/tcp/127.0.0.1/3306) 2>/dev/null
    return $?
}

if mysql_check; then
    echo -e " ${GREEN}OK${NC}"
else
    echo -e " ${YELLOW}Arrete, tentative de demarrage...${NC}"
    STARTED=false

    if command -v brew &> /dev/null && brew services list 2>/dev/null | grep -q mysql; then
        brew services start mysql &>/dev/null && sleep 3 && mysql_check && STARTED=true
    fi

    if [ "$STARTED" = false ] && command -v systemctl &> /dev/null; then
        for SVC in mysql mysql8 mariadb mysqld; do
            sudo systemctl start $SVC &>/dev/null && sleep 3 && mysql_check && STARTED=true && break
        done
    fi

    if [ "$STARTED" = false ] && command -v service &> /dev/null; then
        sudo service mysql start &>/dev/null && sleep 3 && mysql_check && STARTED=true
    fi

    if [ "$STARTED" = true ]; then
        echo -e "      ${GREEN}MySQL demarre.${NC}"
    else
        echo -e "      ${RED}Impossible de demarrer MySQL automatiquement.${NC}"
        echo -e "      ${YELLOW}Demarrez MySQL manuellement puis appuyez sur Entree.${NC}"
        read -r
    fi
fi

# ── Lancement ─────────────────────────────────────────────────────────────────
echo -e "${CYAN}[6/6] Demarrage du serveur Symfony...${NC}"
echo ""

symfony server:start --no-tls --listen-ip=0.0.0.0 &>/dev/null &
sleep 3

# Détecter le port réel
SERVER_URL="http://127.0.0.1:8000"
STATUS_OUTPUT=$(symfony server:status --no-ansi 2>/dev/null)
PORT="8000"

if echo "$STATUS_OUTPUT" | grep -oE "http://127\.0\.0\.1:[0-9]+" &>/dev/null; then
    SERVER_URL=$(echo "$STATUS_OUTPUT" | grep -oE "http://127\.0\.0\.1:[0-9]+" | head -1)
    PORT=$(echo "$SERVER_URL" | grep -oE "[0-9]+$")
fi

# Détecter l'IP locale
LOCAL_IP=$(ip route get 1.1.1.1 2>/dev/null | grep -oP 'src \K\S+' || \
           ipconfig getifaddr en0 2>/dev/null || \
           hostname -I 2>/dev/null | awk '{print $1}')
NETWORK_URL="http://${LOCAL_IP}:${PORT}"

echo -e "  ${GREEN}Acces local   : $SERVER_URL${NC}"
echo -e "  ${YELLOW}Acces reseau  : $NETWORK_URL${NC}"
echo -e "  ${GRAY}(entrez l'adresse reseau sur votre telephone)${NC}"
echo ""
echo -e "  ${GRAY}Appuyez sur Ctrl+C pour arreter le serveur.${NC}"
echo ""

(sleep 1 && (xdg-open "$SERVER_URL" 2>/dev/null || open "$SERVER_URL" 2>/dev/null)) &

symfony server:log
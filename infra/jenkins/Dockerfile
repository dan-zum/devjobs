# Jenkins con PHP 8.2, Composer 2.2, Node 20 y Docker CLI para el pipeline de Laravel
FROM jenkins/jenkins:lts-jdk21

USER root

RUN apt-get update && \
    apt-get install -y --no-install-recommends ca-certificates curl gnupg git unzip && \
    rm -rf /var/lib/apt/lists/*

# PHP 8.2 (repositorio de Ondřej Surý, misma versión que tu proyecto)
RUN curl -sSLo /tmp/sury.deb https://packages.sury.org/debsuryorg-archive-keyring.deb && \
    dpkg -i /tmp/sury.deb && rm /tmp/sury.deb && \
    echo "deb [signed-by=/usr/share/keyrings/debsuryorg-archive-keyring.gpg] https://packages.sury.org/php/ $(. /etc/os-release && echo $VERSION_CODENAME) main" \
      > /etc/apt/sources.list.d/php.list && \
    apt-get update && apt-get install -y --no-install-recommends \
      php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-mysql \
      php8.2-sqlite3 php8.2-bcmath php8.2-intl php8.2-gd php8.2-pcov && \
    rm -rf /var/lib/apt/lists/*

# Composer 2.2 (igual que en tu equipo)
COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

# Node.js 20 para Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y --no-install-recommends nodejs && \
    rm -rf /var/lib/apt/lists/*

# Docker CLI + Docker Compose (usan el Docker de Windows vía el socket montado)
RUN install -m 0755 -d /etc/apt/keyrings && \
    curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc && \
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian $(. /etc/os-release && echo $VERSION_CODENAME) stable" \
      > /etc/apt/sources.list.d/docker.list && \
    apt-get update && apt-get install -y --no-install-recommends docker-ce-cli docker-compose-plugin && \
    rm -rf /var/lib/apt/lists/*

RUN jenkins-plugin-cli --plugins \
    git github workflow-aggregator pipeline-stage-view \
    credentials-binding sonar junit timestamper

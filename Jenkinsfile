pipeline {
    agent any

    environment {
        // Cambia esto por tu usuario de Docker Hub
        DOCKERHUB_USER = 'daniinge'
        APP_IMAGE      = "${DOCKERHUB_USER}/laravel-app"
        WEB_IMAGE      = "${DOCKERHUB_USER}/laravel-web"
        IMAGE_TAG      = "1.0.${BUILD_NUMBER}"
        COMPOSE        = 'docker compose -f docker-compose.prod.yml --env-file .env.prod'
    }

    options {
        timestamps()
        disableConcurrentBuilds()
        buildDiscarder(logRotator(numToKeepStr: '10'))
    }

    stages {

        stage('1. Checkout') {
            steps {
                checkout scm
                sh 'git log -1 --oneline'
            }
        }

        stage('2. Dependencias') {
            steps {
                sh 'php -v && composer --version && node -v'
                sh 'composer install --no-interaction --prefer-dist --no-progress'
                sh 'if [ -f package-lock.json ]; then npm ci; else npm install; fi'
            }
        }

        stage('3. Compilar assets (Vite)') {
            steps {
                sh 'npm run build'
            }
        }

        stage('4. Pruebas (PHPUnit)') {
            environment {
                APP_ENV          = 'testing'
                DB_CONNECTION    = 'sqlite'
                DB_DATABASE      = ':memory:'
                CACHE_DRIVER     = 'array'
                SESSION_DRIVER   = 'array'
                QUEUE_CONNECTION = 'sync'
                MAIL_MAILER      = 'array'
            }
            steps {
                sh 'cp .env.example .env && php artisan key:generate --force'
                sh '''
                    mkdir -p reports
                    # Usa Pest si el proyecto lo tiene; si no, PHPUnit
                    if [ -f vendor/bin/pest ]; then RUNNER=vendor/bin/pest; else RUNNER=vendor/bin/phpunit; fi
                    php -d pcov.enabled=1 -d pcov.directory=app $RUNNER \
                        --coverage-clover reports/coverage.xml \
                        --log-junit reports/junit.xml
                '''
            }
            post {
                always {
                    junit allowEmptyResults: true, testResults: 'reports/junit.xml'
                }
            }
        }

        stage('5. Análisis SonarQube') {
            steps {
                script {
                    def scannerHome = tool 'SonarScanner'
                    withSonarQubeEnv('SonarQube') {
                        sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectVersion=${IMAGE_TAG}"
                    }
                }
            }
        }

        stage('6. Quality Gate') {
            steps {
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('7. Construir imágenes Docker') {
            steps {
                sh "docker build --target app -t ${APP_IMAGE}:${IMAGE_TAG} -t ${APP_IMAGE}:latest ."
                sh "docker build --target web -t ${WEB_IMAGE}:${IMAGE_TAG} -t ${WEB_IMAGE}:latest ."
            }
        }

        stage('8. Publicar en Docker Hub') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-creds',
                                                  usernameVariable: 'DH_USER',
                                                  passwordVariable: 'DH_PASS')]) {
                    sh 'echo "$DH_PASS" | docker login -u "$DH_USER" --password-stdin'
                    sh "docker push ${APP_IMAGE}:${IMAGE_TAG} && docker push ${APP_IMAGE}:latest"
                    sh "docker push ${WEB_IMAGE}:${IMAGE_TAG} && docker push ${WEB_IMAGE}:latest"
                }
            }
        }

        stage('9. Desplegar (CD)') {
            steps {
                withCredentials([file(credentialsId: 'laravel-env-prod', variable: 'ENV_FILE')]) {
                    sh 'cp "$ENV_FILE" .env.prod'
                }
                sh "${COMPOSE} up -d --remove-orphans"
                sh "${COMPOSE} exec -T app php artisan migrate --force"
            }
        }

        stage('10. Verificación') {
            steps {
                retry(10) {
                    sleep 5
                    sh 'curl -fsS http://laravel-web/health'
                }
                sh "docker inspect --format='laravel-web: {{.State.Health.Status}}' laravel-web"
                sh 'docker ps --filter name=laravel- --format "table {{.Names}}\\t{{.Image}}\\t{{.Status}}"'
                sh 'docker logs --tail 20 laravel-web'
                sh 'docker logs --tail 20 laravel-app'
            }
        }
    }

    post {
        success {
            echo "✅ Despliegue OK: http://localhost:8081  (versión ${IMAGE_TAG})"
        }
        failure {
            echo '❌ El pipeline falló. Revisa la etapa marcada en rojo.'
            sh 'docker logs --tail 50 laravel-app || true'
        }
        always {
            sh 'rm -f .env.prod .env'
            sh 'docker logout || true'
        }
    }
}
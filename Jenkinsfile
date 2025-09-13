pipeline {
    agent any 
    
    stages { 
        stage('SCM Checkout') {
            steps {
                retry(3) {
                    git branch: 'main', url: 'https://github.com/Sandalanka/Payment-Notification-Payout-System.git'
                }
            }
        }
        stage('Build Docker Image') {
            steps {  
                bat 'docker build -t sandalanka/payment_app:%BUILD_NUMBER% .'
            }
        }
        stage('Login to Docker Hub') {
            steps {
                withCredentials([string(credentialsId: 'payment-system', variable: 'payment-system')]) {
                    script {
                        bat "docker login -u sandalanka -p %payment-system%"
                    }
                }
            }
        }
        stage('Push Image') {
            steps {
                bat 'docker push sandalanka/payment_app:%BUILD_NUMBER%'
            }
        }
    }
    post {
        always {
            bat 'docker logout'
        }
    }
}
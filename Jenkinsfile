pipeline {
    agent any

    environment {
        IMAGE_NAME = "minhtri25/pawspa-devops"
        IMAGE_TAG = "${BUILD_NUMBER}"
        CONTAINER_NAME = "pawspa-app"
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Composer Validate') {
            steps {
                sh 'composer validate --no-check-lock'
            }
        }

        stage('PHP Syntax Check') {
            steps {
                sh 'find src public -name "*.php" -print0 | xargs -0 -n1 php -l'
            }
        }

        stage('Docker Build') {
            steps {
                sh 'docker build -t $IMAGE_NAME:$IMAGE_TAG -t $IMAGE_NAME:latest .'
            }
        }

        stage('Docker Login') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-credentials', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
                    sh 'echo $DOCKER_PASS | docker login -u $DOCKER_USER --password-stdin'
                }
            }
        }

        stage('Docker Push') {
            steps {
                sh 'docker push $IMAGE_NAME:$IMAGE_TAG'
                sh 'docker push $IMAGE_NAME:latest'
            }
        }

        stage('Deploy') {
            steps {
                // Tạo file .env giả lập từ .env.example vì Git không lưu file .env
                sh 'cp .env.example .env || touch .env'
                
                // Chỉ build và khởi động lại service 'app' (chứa code PHP mới)
                // Phải chỉ định -p du-an-web để Docker hiểu là đang cập nhật project gốc chứ không phải tạo project mới (gây xung đột tên container)
                sh 'docker-compose -p du-an-web up -d --build app'
            }
        }
    }

    post {
        success {
            echo 'Pipeline completed successfully. Pawspa has been deployed.'
        }
        failure {
            echo 'Pipeline failed. Please check logs.'
        }
    }
}

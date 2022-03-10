pipeline {
  agent {label "PODS-DEV"}

   environment {

    }
   stages {
      stage('Notify Bitbucket Status') {
        steps {
                bitbucketStatusNotify(buildState: 'INPROGRESS')
        }
      } 
      stage("Build Docker Deploy Image") {
        // We can build the "pods" image 
        when {
            anyOf {
                branch 'develop_refactor';
            }
        }
        //build deploy image for develop and release branch only
        steps {
          sh "docker build . -t pods"
        }
      }
      
      stage("Reboot PODS"){
         when {
            anyOf {
                branch 'develop_refactor';
            }
          }
          // Dockerfile needs to be modified to enable a service file maybe.
          // Right now Jenkins blocks on the process
          // Tried running this as a backgrounded task via "&" but that causes the build to fail silently
          // sh "docker run -d -p 85:80 pods"
          steps {
            sh "./run-pods.sh"
          }
      }
   }
  post {
     // Let bitbucket know the final result
      success {
            bitbucketStatusNotify(buildState: 'SUCCESSFUL')
      }
      failure {
            bitbucketStatusNotify(buildState: 'FAILED')
      }
      always {
       echo "CI/CD pipeline finished"
      }
      cleanup{
        deleteDir()
      }
  }
}

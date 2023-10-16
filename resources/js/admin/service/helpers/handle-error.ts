import { AxiosError } from "axios"

class HandleError {
    axiosError(error: AxiosError) {
        if (error.response) {
            const statusCode = error.response.status
            if (statusCode === 404) {
                console.log('Not found: The requested resource was not found.');
            } else if (statusCode === 429) {
                console.log('Rate Limited: Too many requests, please try again later.');
            } else if (statusCode >= 500) {
                console.log('Server Error: An internal server error occurred.');
            } else if (error.message === 'Network Error') {
                console.log('No internet connection. Please check your network connection.');
            }
        } else if (error.request) {
            console.log('No response received from the server.');
        } else {
            console.log('An unexpected error occurred:');
            console.log(error)
        }
    }
    firebaseError() {
        console.log()
    }
}

export const handleError = new HandleError()
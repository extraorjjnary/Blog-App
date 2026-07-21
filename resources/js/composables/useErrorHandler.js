export function useErrorHandler() {
    const getErrorMessage = (error, fallback = "Something went wrong.") => {
        if (error.response?.data?.errors) {
            const firstError = Object.values(error.response.data.errors)[0];
            return Array.isArray(firstError) ? firstError[0] : firstError;
        }

        if (error.response?.data?.message) {
            return error.response.data.message;
        }

        if (!error.response) {
            return "Network error. Please check your connection.";
        }

        return fallback;
    };

    return { getErrorMessage };
}

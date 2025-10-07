import http from '@/api/http';

export interface RegisterResponse {
    complete: boolean;
    intended?: string;
    confirmationToken?: string;
}

export interface RegisterData {
    username: string;
    email: string;
    first_name: string;
    last_name: string;
    password: string;
    recaptchaData?: string | null;
}

export default ({ username, password, email, first_name, last_name, recaptchaData }: RegisterData): Promise<RegisterResponse> => {
    return new Promise((resolve, reject) => {
        http.get('/sanctum/csrf-cookie')
            .then(() =>
                http.post('/auth/register', {
                    first_name,
                    last_name,
                    email,
                    username,
                    password,
                    'g-recaptcha-response': recaptchaData,
                })
            )
            .then((response) => {
                if (!(response.data instanceof Object)) {
                    return reject(new Error('An error occurred while processing the login request.'));
                }

                return resolve({
                    complete: response.data.data.complete,
                    intended: response.data.data.intended || undefined,
                    confirmationToken: response.data.data.confirmation_token || undefined,
                });
            })
            .catch(reject);
    });
};

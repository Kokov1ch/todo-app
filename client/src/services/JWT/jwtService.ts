import Cookies from 'universal-cookie';

export function setJWT(JWTToken: string): void {
    const cookies = new Cookies();
    cookies.set('JWT-Token', JWTToken, { path: '/' });
}

export function getJWT(): void {
    const cookies = new Cookies();
    return cookies.get('JWT-Token');
}

export function removeJWT(): void {
    const cookies = new Cookies();
    cookies.remove('JWT-Token', { path: '/' });
}

export function setRefreshToken(refreshToken: string): void {
    const cookies = new Cookies();
    cookies.set('refresh_token', refreshToken, { path: '/' });
}

export function getRefreshToken(): string | undefined {
    const cookies = new Cookies();
    return cookies.get('refresh_token');
}

export function removeRefreshToken(): void {
    const cookies = new Cookies();
    return cookies.remove('refresh_token', { path: '/' });
}
import { ApiError, ApiErrorBody } from '@/types/api';

const BASE_URL = import.meta.env.VITE_API_BASE_URL as string;
const TOKEN_KEY = 'auth_token';

export function getStoredToken(): string | null {
  return localStorage.getItem(TOKEN_KEY);
}

export function setStoredToken(token: string): void {
  localStorage.setItem(TOKEN_KEY, token);
}

export function clearStoredToken(): void {
  localStorage.removeItem(TOKEN_KEY);
}

// ── Core fetch wrapper ────────────────────────────────────────────────────────

async function request<T>(
  method: string,
  path: string,
  body?: unknown,
): Promise<T> {
  if (!BASE_URL) {
    throw new Error('VITE_API_BASE_URL is not configured');
  }

  const token = getStoredToken();

  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const res = await fetch(`${BASE_URL}${path}`, {
    method,
    headers,
    body: body !== undefined ? JSON.stringify(body) : undefined,
  });

  if (res.status === 204) {
    return undefined as T;
  }

  const contentType = res.headers.get('content-type') ?? '';
  const hasJsonBody = contentType.includes('application/json');
  const json = hasJsonBody ? await res.json() : null;

  if (!res.ok) {
    if (json && typeof json === 'object' && 'error' in json) {
      throw new ApiError(res.status, json.error as ApiErrorBody);
    }

    throw new ApiError(res.status, {
      code: 'unknown_error',
      message: `Request failed with status ${res.status}`,
      correlation_id: null,
    });
  }

  return json as T;
}

// ── Convenience helpers ───────────────────────────────────────────────────────

export const api = {
  get: <T>(path: string) => request<T>('GET', path),
  post: <T>(path: string, body?: unknown) => request<T>('POST', path, body),
  put: <T>(path: string, body?: unknown) => request<T>('PUT', path, body),
  delete: <T>(path: string) => request<T>('DELETE', path),
};

// ── Auth endpoints ────────────────────────────────────────────────────────────

import type { AuthResponse, ApiResponse, User } from '@/types/api';

export const authApi = {
  login: (email: string, password: string) =>
    api.post<AuthResponse>('/auth/login', { email, password }),

  register: (payload: {
    name: string;
    surname: string;
    birth_date: string;
    email: string;
    password: string;
    password_confirmation: string;
  }) => api.post<AuthResponse>('/auth/register', payload),

  me: () => api.get<ApiResponse<User>>('/me'),

  loginStreak: () =>
    api.get<ApiResponse<{ weekDays: boolean[]; totalCoins: number }>>('/me/login-streak'),

  logout: () => api.post<void>('/auth/logout'),
};

// ── Catalog endpoints ─────────────────────────────────────────────────────────

import type { Product, Category, PaginatedResponse } from '@/types/api';

export interface ProductFilters {
  categoryId?: string;
  companyId?: string;
  q?: string;
  perPage?: number;
  page?: number;
}

export const catalogApi = {
  products: (filters: ProductFilters = {}) => {
    const params = new URLSearchParams();
    if (filters.categoryId) params.set('categoryId', filters.categoryId);
    if (filters.companyId) params.set('companyId', filters.companyId);
    if (filters.q) params.set('q', filters.q);
    if (filters.perPage) params.set('perPage', String(filters.perPage));
    if (filters.page) params.set('page', String(filters.page));
    const qs = params.toString();
    return api.get<PaginatedResponse<Product>>(`/products${qs ? `?${qs}` : ''}`);
  },

  product: (id: string) =>
    api.get<{ data: Product }>(`/products/${id}`),

  categories: () =>
    api.get<{ data: Category[] }>('/categories'),
};

// ── Orders endpoints ──────────────────────────────────────────────────────────

import type { Order, WriteResponse } from '@/types/api';

export const ordersApi = {
  create: (items: { productId: string; quantity: number }[]) =>
    api.post<WriteResponse<Order>>('/orders', { items }),

  list: (params: { status?: string; page?: number; perPage?: number } = {}) => {
    const qs = new URLSearchParams();
    if (params.status) qs.set('status', params.status);
    if (params.page) qs.set('page', String(params.page));
    if (params.perPage) qs.set('perPage', String(params.perPage));
    const q = qs.toString();
    return api.get<PaginatedResponse<Order>>(`/orders${q ? `?${q}` : ''}`);
  },

  get: (id: string) =>
    api.get<{ data: Order }>(`/orders/${id}`),

  pay: (id: string) =>
    api.post<WriteResponse<Order>>(`/orders/${id}/pay`),

  cancel: (id: string) =>
    api.post<WriteResponse<Order>>(`/orders/${id}/cancel`),
};

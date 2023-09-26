export type Responses<T> = { status: 200; data: T } | { status: 301; to: string } | { status: 400; error: Error };

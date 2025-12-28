import { clsx, type ClassValue } from "clsx"
import { twMerge } from "tailwind-merge"

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function formatCurrency(amount: number, locale: string = 'en-US', currency: string = 'USD') {
  return amount.toLocaleString(locale, { style: 'currency', currency: currency })
}

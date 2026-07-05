import { clsx, type ClassValue } from "clsx"
import { twMerge } from "tailwind-merge"

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function formatCurrency(amount: number, locale: string = 'en-US', currency: string = 'USD') {
  return amount.toLocaleString(locale, { style: 'currency', currency: currency })
}

export function formatEventDate(start: string, end: string) {
  const startDate = new Date(start)
  const endDate = new Date(end)
  const dateOpts: Intl.DateTimeFormatOptions = { month: 'short', day: 'numeric', year: 'numeric' }
  const timeOpts: Intl.DateTimeFormatOptions = { hour: 'numeric', minute: '2-digit' }

  if (startDate.toDateString() === endDate.toDateString()) {
    return `${startDate.toLocaleDateString('en-US', dateOpts)} · ${startDate.toLocaleTimeString('en-US', timeOpts)} – ${endDate.toLocaleTimeString('en-US', timeOpts)}`
  }

  return `${startDate.toLocaleDateString('en-US', dateOpts)} – ${endDate.toLocaleDateString('en-US', dateOpts)}`
}

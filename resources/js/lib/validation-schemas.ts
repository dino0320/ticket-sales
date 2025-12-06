import { z } from 'zod'

export const emailSchema = z.string().email({ message: 'Invalid email address' })

export const passwordSchema = z
  .string()
  .min(8, { message: 'Password must be at least 8 characters long' })
  .regex(/^[a-zA-Z0-9]+$/, { message: 'Password must be alphanumeric' })

export const nameSchema = z
  .string()
  .min(2, { message: 'Name must be at least 2 characters long' })

export const messageSchema = z
  .string()
  .min(10, { message: 'Message must be at least 10 characters long' })

export const contactFormSchema = z.object({
  name: nameSchema,
  email: emailSchema,
  message: messageSchema,
})

export const loginFormSchema = z.object({
  email: emailSchema,
  password: passwordSchema,
})

export const registerFormSchema = z
  .object({
    name: nameSchema,
    email: emailSchema,
    password: passwordSchema,
    password_confirmation: z.string(),
  })
  .refine((data) => data.password === data.password_confirmation, {
    path: ['password_confirmation'],
    message: 'Passwords do not match',
  })

export const resetPasswordFormSchema = z
  .object({
    email: emailSchema,
    password: passwordSchema,
    new_password: passwordSchema,
    new_password_confirmation: z.string(),
  })
  .refine((data) => data.new_password === data.new_password_confirmation, {
    path: ['new_password_confirmation'],
    message: 'Passwords do not match',
  })
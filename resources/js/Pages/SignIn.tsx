import { authenticate } from '@/actions/App/Http/Controllers/SignInController';
import { signUp } from '@/routes/index';
import { Link, router } from '@inertiajs/react'
import { z } from 'zod'
import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import { useState } from 'react';
import { setManualFormErrors } from '@/lib/form-utils'

import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { PasswordInput } from '@/components/ui/password-input'

import { LoadingButton } from '@/components/loading-button'

import { loginFormSchema } from '@/lib/validation-schemas'

const formSchema = loginFormSchema

export default function SignIn() {
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      email: '',
      password: '',
    },
  })

  const [errorMessage, setErrorMessage] = useState('')
  const [isLoading, setIsLoading] = useState(false)

  async function onSubmit(values: z.infer<typeof formSchema>) {
    setIsLoading(true)
    router.post(authenticate(), values, {
      onError: (errors: Record<string, string>) => setManualFormErrors(errors, form, setErrorMessage),
      onFinish: () => setIsLoading(false)
    })
  }

  return (
    <div className="flex min-h-[60vh] h-full w-full items-center justify-center px-4">
      <Card className="mx-auto max-w-sm">
        <CardHeader>
          <CardTitle className="text-2xl">Sign in</CardTitle>
          <CardDescription>
            Sign in with your existing account by filling out the form below.
            <p className="text-destructive text-sm">{errorMessage}</p>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-8">
              <div className="grid gap-4">
                {/* Email Field */}
                <FormField
                  control={form.control}
                  name="email"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="email">Email</FormLabel>
                      <FormControl>
                        <Input
                          id="email"
                          placeholder="johndoe@mail.com"
                          type="email"
                          autoComplete="email"
                          {...field}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* Password Field */}
                <FormField
                  control={form.control}
                  name="password"
                  render={({ field }) => (
                    <FormItem className="grid gap-2">
                      <FormLabel htmlFor="password">Password</FormLabel>
                      <FormControl>
                        <PasswordInput
                          id="password"
                          placeholder="******"
                          autoComplete="new-password"
                          {...field}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <LoadingButton type="submit" className="w-full" isLoading={isLoading}>
                  Sign in
                </LoadingButton>
              </div>
            </form>
          </Form>
          {
          <div className="mt-4 text-center text-sm">
            Don't have your account?{' '}
            <Link href={signUp()}>
              Sign up
            </Link>
          </div>
          }
        </CardContent>
      </Card>
    </div>
  )
}

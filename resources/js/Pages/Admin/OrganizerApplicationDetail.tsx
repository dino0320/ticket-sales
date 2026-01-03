import { updateStatus } from '@/actions/App/Http/Controllers/Admin/OrganizerApplicationController'
import { router } from '@inertiajs/react'
import { useState } from 'react'
import { LoadingButton } from '@/components/loading-button'
import { OrganizerApplication } from '@/components/organizer-application'
import type { OrganizerApplicationData } from '@/components/organizer-application'

export default function OrganizerApplicationDetail({ userOrganizerApplication }: { userOrganizerApplication: OrganizerApplicationData}) {
  const [isLoading, setIsLoading] = useState(false)

  async function onClick(isApproved: boolean) {
    setIsLoading(true)
    router.put(updateStatus(userOrganizerApplication.id), { is_approved: isApproved }, {
      onFinish: () => setIsLoading(false)
    })
  }

  return (
    <div className="space-y-1">
      <OrganizerApplication userOrganizerApplication={userOrganizerApplication}/>
      <div className="flex gap-1">
        <LoadingButton onClick={() => onClick(true)} isLoading={isLoading}>Approve</LoadingButton>
        <LoadingButton onClick={() => onClick(false)} isLoading={isLoading}>Reject</LoadingButton>
      </div>
    </div>
  )
}

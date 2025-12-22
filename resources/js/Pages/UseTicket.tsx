export default function UseTicket({ userTicketId }: { userTicketId: number}) {
  return (
    <div>
      <p>The ticket has been used.</p>

      <p>User Ticket ID: {userTicketId}</p>
    </div>
  )
}

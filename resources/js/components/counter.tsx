import { CiCirclePlus, CiCircleMinus } from 'react-icons/ci';

export function Counter({ number, ticketId, updateNumber }: { number: number, ticketId: number, updateNumber: (number: number, ticketId: number) => void }) {
  async function onClick(isPlus: boolean = true) {
    try {
      updateNumber(isPlus ? number + 1 : number - 1, ticketId)
    } catch (error) {
      console.error('Can\'t update the number', error)
    }
  }

  return (
    <div>
      <CiCircleMinus onClick={() => onClick(false)}/>{number}<CiCirclePlus onClick={() => onClick()}/>
    </div>
  )
}

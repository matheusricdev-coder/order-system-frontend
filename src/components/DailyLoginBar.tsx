import { Check } from "lucide-react";

const weekDays = ["D", "S", "T", "Q", "Q", "S", "S"];

interface DailyLoginBarProps {
  loginDays: boolean[];
  currentDay: number;
}

const DailyLoginBar = ({ loginDays, currentDay }: DailyLoginBarProps) => {
  return (
    <div className="bg-card border-b border-border">
      <div className="container py-3">
        <div className="flex items-center justify-between gap-4">
          <div className="flex items-center gap-2">
            <span className="text-lg">🔥</span>
            <span className="text-sm font-semibold text-foreground">Login Diário</span>
          </div>
          
          <div className="flex items-center gap-1.5">
            {weekDays.map((day, index) => {
              const isCompleted = loginDays[index];
              const isCurrent = index === currentDay;
              
              return (
                <div
                  key={index}
                  className={`
                    relative w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all
                    ${isCompleted 
                      ? "bg-success text-success-foreground" 
                      : isCurrent
                        ? "bg-brand text-primary-foreground ring-2 ring-brand ring-offset-2 ring-offset-card"
                        : "bg-secondary text-muted-foreground"
                    }
                  `}
                >
                  {isCompleted ? (
                    <Check className="w-4 h-4" />
                  ) : (
                    day
                  )}
                </div>
              );
            })}
          </div>

          <div className="flex items-center gap-1.5 bg-brand-light px-3 py-1.5 rounded-full">
            <span className="text-base">🪙</span>
            <span className="text-sm font-bold text-brand">+5/dia</span>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DailyLoginBar;

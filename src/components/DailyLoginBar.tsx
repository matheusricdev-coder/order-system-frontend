import { Check } from "lucide-react";

const weekDays = ["D", "S", "T", "Q", "Q", "S", "S"];

interface DailyLoginBarProps {
  loginDays: boolean[];
  currentDay: number;
}

const DailyLoginBar = ({ loginDays, currentDay }: DailyLoginBarProps) => {
  const completedDays = loginDays.filter(Boolean).length;

  return (
    <div className="bg-gradient-to-r from-brand-light to-card border-b border-border">
      <div className="container py-4">
        <div className="flex flex-col gap-3">
          {/* Header with title and coins */}
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2">
              <span className="text-xl">🔥</span>
              <div>
                <h3 className="text-sm font-bold text-foreground">Login Diário</h3>
                <p className="text-xs text-muted-foreground">
                  Logue todos os dias para ganhar moedas e trocar por frete grátis ou produtos!
                </p>
              </div>
            </div>
            <div className="flex items-center gap-1 bg-brand px-3 py-1.5 rounded-full shadow-button">
              <span className="text-sm">🪙</span>
              <span className="text-xs font-bold text-primary-foreground">+5</span>
            </div>
          </div>

          {/* Days row */}
          <div className="flex items-center gap-2">
            <div className="flex items-center gap-1.5 flex-1">
              {weekDays.map((day, index) => {
                const isCompleted = loginDays[index];
                const isCurrent = index === currentDay;
                
                return (
                  <div
                    key={index}
                    className={`
                      relative flex-1 max-w-10 aspect-square rounded-full flex items-center justify-center text-xs font-bold transition-all
                      ${isCompleted 
                        ? "bg-success text-success-foreground" 
                        : isCurrent
                          ? "bg-brand text-primary-foreground ring-2 ring-brand ring-offset-1 ring-offset-background"
                          : "bg-secondary text-muted-foreground"
                      }
                    `}
                  >
                    {isCompleted ? (
                      <Check className="w-3.5 h-3.5" />
                    ) : (
                      day
                    )}
                  </div>
                );
              })}
            </div>
            <span className="text-xs text-muted-foreground font-medium whitespace-nowrap">
              {completedDays}/7 dias
            </span>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DailyLoginBar;

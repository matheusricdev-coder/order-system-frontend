import { ShoppingCart, User, Menu, LogOut } from "lucide-react";
import SearchBar from "./SearchBar";
import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "@/contexts/AuthContext";

interface HeaderProps {
  coinBalance?: number;
  onSearch?: (q: string) => void;
}

const Header = ({ coinBalance = 0, onSearch }: HeaderProps) => {
  const [searchQuery, setSearchQuery] = useState("");
  const navigate = useNavigate();
  const { user, isAuthenticated, logout } = useAuth();

  const handleSearch = (q: string) => {
    setSearchQuery(q);
    onSearch?.(q);
  };

  const handleLogout = async () => {
    await logout();
    navigate("/login");
  };

  return (
    <header className="sticky top-0 z-50 bg-brand shadow-sm">
      <div className="container py-3">
        <div className="flex items-center gap-3">
          <button className="lg:hidden p-2 -ml-2">
            <Menu className="w-6 h-6 text-primary-foreground" />
          </button>
          
          <Link to="/" className="flex-shrink-0">
            <h1 className="text-xl font-bold text-primary-foreground tracking-tight">
              MarketPlace
            </h1>
          </Link>

          <div className="flex-1 hidden sm:block">
            <SearchBar
              value={searchQuery}
              onChange={handleSearch}
              onSearch={() => onSearch?.(searchQuery)}
            />
          </div>

          <div className="flex items-center gap-1">
            {/* Coin Balance */}
            <div className="flex items-center gap-1 bg-primary-foreground/20 backdrop-blur-sm px-2.5 py-1.5 rounded-full mr-1">
              <span className="text-base">🪙</span>
              <span className="text-sm font-bold text-primary-foreground">{coinBalance}</span>
            </div>

            {isAuthenticated ? (
              <>
                <span className="hidden sm:block text-sm text-primary-foreground/80 mr-1">
                  Olá, {user?.name}
                </span>
                <button
                  onClick={handleLogout}
                  className="p-2 hover:bg-brand-hover rounded-lg transition-colors"
                  title="Sair"
                >
                  <LogOut className="w-6 h-6 text-primary-foreground" />
                </button>
              </>
            ) : (
              <button
                onClick={() => navigate("/login")}
                className="p-2 hover:bg-brand-hover rounded-lg transition-colors"
                title="Entrar"
              >
                <User className="w-6 h-6 text-primary-foreground" />
              </button>
            )}

            <button className="p-2 hover:bg-brand-hover rounded-lg transition-colors relative">
              <ShoppingCart className="w-6 h-6 text-primary-foreground" />
              <span className="absolute -top-0.5 -right-0.5 bg-destructive text-destructive-foreground text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full">
                3
              </span>
            </button>
          </div>
        </div>

        <div className="mt-3 sm:hidden">
          <SearchBar
            value={searchQuery}
            onChange={handleSearch}
            onSearch={() => onSearch?.(searchQuery)}
          />
        </div>
      </div>
    </header>
  );
};

export default Header;
